import mysql.connector
from mysql.connector import Error
from datetime import date
from config import DB_CONFIG


def get_connection():
    return mysql.connector.connect(**DB_CONFIG)


def insert_listings(listings: list[dict]) -> int:
    """
    Insert listings into the market_listings table.
    Each listing dict should have: model, storage, price, city, source, title, url, collected_at
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

        for listing in listings:
            try:
                cursor.execute(query, (
                    listing['model'],
                    listing['storage'],
                    listing['price'],
                    listing['city'],
                    listing['source'],
                    listing.get('title', ''),
                    listing.get('url', ''),
                    listing.get('collected_at', date.today().isoformat()),
                ))
                inserted += 1
            except Error:
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
