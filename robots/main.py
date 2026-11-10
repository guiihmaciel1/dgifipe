#!/usr/bin/env python3
"""
DG iFipe Robot
Marketplace scraper. Runs daily via cron to collect iPhone listings
from Facebook Marketplace. Communicates with Laravel via shared MySQL database.
"""

import logging
import time
import traceback
from datetime import datetime

from playwright.sync_api import sync_playwright

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(name)s: %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S',
)

from config import MODELS, CITIES, HEADLESS, USER_AGENT
from database import insert_listings, cleanup_old_listings
from scraper_facebook import scrape_facebook


def create_browser_page(playwright):
    """Create a reusable browser and page instance."""
    browser = playwright.chromium.launch(headless=HEADLESS)
    context = browser.new_context(
        locale='pt-BR',
        user_agent=USER_AGENT,
        viewport={'width': 1366, 'height': 768},
    )
    page = context.new_page()
    return browser, page


def run_facebook(page, model, storage, city):
    """Run Facebook scraper and insert results. Returns (count, error)."""
    try:
        listings = scrape_facebook(page, model, storage, city)
        if listings:
            count = insert_listings(listings)
            return count, None
        return 0, None
    except Exception as e:
        return 0, str(e)


def main():
    start_time = time.time()
    total_ads = 0
    total_errors = 0

    print(f"[{datetime.now()}] Robot starting... Source: Facebook")

    try:
        deleted = cleanup_old_listings(days=30)
        if deleted > 0:
            print(f"  Cleaned up {deleted} old listings")
    except Exception as e:
        print(f"  Cleanup error: {e}")

    with sync_playwright() as p:
        browser, page = create_browser_page(p)

        try:
            for model, storages in MODELS.items():
                for storage in storages:
                    for city in CITIES:
                        label = f"{model} {storage} - {city}"
                        count, err = run_facebook(page, model, storage, city)
                        if err:
                            total_errors += 1
                            print(f"  FB ERROR: {label} -> {err}")
                        elif count > 0:
                            total_ads += count
                            print(f"  FB: {label} -> {count} ads")

        finally:
            browser.close()

    elapsed = time.time() - start_time
    print(
        f"[{datetime.now()}] Robot finished. "
        f"Ads: {total_ads}, Errors: {total_errors}, Time: {elapsed:.0f}s"
    )


if __name__ == '__main__':
    try:
        main()
    except Exception as e:
        print(f"FATAL ERROR: {type(e).__name__}: {str(e)[:200]}")
        print(traceback.format_exc())
