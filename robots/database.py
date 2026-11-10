import logging
import mysql.connector
from mysql.connector import Error
from datetime import date
from config import DB_CONFIG
from listing_filter import should_skip

logger = logging.getLogger(__name__)


def get_connection():
    return mysql.connector.connect(**DB_CONFIG)


def _collected_at(listing: dict) -> str:
    return listing.get('collected_at', date.today().isoformat())


def _existing_keys(cursor, listings: list[dict]) -> set[tuple[str, str]]:
    """Return (url, collected_at) pairs from the batch already stored."""
    urls = list({l['url'] for l in listings if l.get('url')})
    if not urls:
        return set()

    placeholders = ','.join(['%s'] * len(urls))
    cursor.execute(
        f"SELECT url, collected_at FROM market_listings WHERE url IN ({placeholders})",
        urls,
    )
    return {(url, str(collected_at)) for url, collected_at in cursor.fetchall()}


def insert_listings(listings: list[dict]) -> int:
    """
    Insert listings into the market_listings table.
    Skips sealed devices, listings whose title contradicts the
    searched model/storage and URLs already collected on the same day.
    Returns the number of inserted rows.
    """
    if not listings:
        return 0

    conn = None
    inserted = 0
    try:
        conn = get_connection()
        cursor = conn.cursor()

        query = """
            INSERT INTO market_listings
                (model, storage, price, city, source, title, url, collected_at, created_at, updated_at)
            VALUES
                (%s, %s, %s, %s, %s, %s, %s, %s, NOW(), NOW())
        """

        seen = _existing_keys(cursor, listings)

        for listing in listings:
            if should_skip(listing):
                continue

            url = listing.get('url', '')
            key = (url, _collected_at(listing))
            if url and key in seen:
                continue

            try:
                cursor.execute(query, (
                    listing['model'],
                    listing['storage'],
                    listing['price'],
                    listing['city'],
                    listing['source'],
                    listing.get('title', ''),
                    url,
                    _collected_at(listing),
                ))
                inserted += 1
                seen.add(key)
            except Error as row_err:
                logger.warning(
                    "Insert failed for %s %s (%s): %s",
                    listing.get('model'), listing.get('storage'),
                    listing.get('source'), row_err,
                )
                continue

        conn.commit()
    except Error as e:
        raise e
    finally:
        if conn and conn.is_connected():
            cursor.close()
            conn.close()

    return inserted


def cleanup_old_listings(days: int = 30) -> int:
    """Remove listings older than specified days. Returns deleted count."""
    conn = None
    try:
        conn = get_connection()
        cursor = conn.cursor()
        cursor.execute(
            "DELETE FROM market_listings WHERE collected_at < DATE_SUB(CURDATE(), INTERVAL %s DAY)",
            (days,)
        )
        deleted = cursor.rowcount
        conn.commit()
        return deleted
    except Error as e:
        raise e
    finally:
        if conn and conn.is_connected():
            cursor.close()
            conn.close()
