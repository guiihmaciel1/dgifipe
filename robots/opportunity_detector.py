import logging
import math
import mysql.connector
from mysql.connector import Error
from config import DB_CONFIG

logger = logging.getLogger(__name__)

DEFAULT_MARGIN = 15.0
MIN_LISTINGS_FOR_ALERT = 5
MIN_PROFIT_PERCENTAGE = 10.0


def check_opportunity(listing: dict) -> None:
    """
    After inserting a listing, check if its price is significantly
    below the market average for the same model/storage. If so,
    create an opportunity alert.
    """
    conn = None
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        cursor = conn.cursor(dictionary=True)

        cursor.execute(
            """
            SELECT AVG(price) as avg_price, COUNT(*) as total
            FROM market_listings
            WHERE model = %s AND storage = %s
              AND collected_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
              AND title NOT REGEXP '(lacrado|lacrada|selado|selada|sealed|novo na caixa|zero na caixa)'
            """,
            (listing['model'], listing['storage']),
        )
        row = cursor.fetchone()

        if not row or not row['avg_price'] or row['total'] < MIN_LISTINGS_FOR_ALERT:
            return

        market_avg = float(row['avg_price'])
        buy_price = math.floor(market_avg * (1 - DEFAULT_MARGIN / 100) / 100) * 100
        listing_price = float(listing['price'])

        if listing_price >= buy_price:
            return

        profit = buy_price - listing_price
        profit_pct = (profit / listing_price) * 100

        if profit_pct < MIN_PROFIT_PERCENTAGE:
            return

        cursor.execute(
            """
            SELECT id FROM opportunity_alerts
            WHERE url = %s AND url != '' AND url IS NOT NULL
            LIMIT 1
            """,
            (listing.get('url', ''),),
        )
        if cursor.fetchone():
            return

        cursor.execute(
            """
            INSERT INTO opportunity_alerts
                (model, storage, listing_price, market_average, suggested_buy_price,
                 potential_profit, profit_percentage, source, city, title, url,
                 status, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, 'new', NOW(), NOW())
            """,
            (
                listing['model'],
                listing['storage'],
                listing_price,
                round(market_avg, 2),
                buy_price,
                round(profit, 2),
                round(profit_pct, 2),
                listing['source'],
                listing['city'],
                listing.get('title', '')[:255],
                listing.get('url', '')[:500],
            ),
        )
        conn.commit()

        logger.info(
            "OPPORTUNITY: %s %s at R$%.0f (market R$%.0f, buy R$%.0f, profit %.0f%%)",
            listing['model'], listing['storage'],
            listing_price, market_avg, buy_price, profit_pct,
        )

    except Error as e:
        logger.warning("Opportunity check failed: %s", e)
    finally:
        if conn and conn.is_connected():
            cursor.close()
            conn.close()
