#!/usr/bin/env python3
"""
DG iFipe Robot
Marketplace scraper. Runs daily via cron to collect iPhone listings
from Facebook Marketplace and OLX. Communicates with Laravel via shared MySQL database.
"""

import time
import traceback
from datetime import datetime

from playwright.sync_api import sync_playwright

from config import MODELS, CITIES, HEADLESS, USER_AGENT
from database import insert_listings, cleanup_old_listings
from scraper_facebook import scrape_facebook
from scraper_olx import scrape_olx
from telegram_logger import notify_start, notify_finish, notify_error


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


def run_olx(page, model, storage, city):
    """Run OLX scraper and insert results. Returns (count, error)."""
    try:
        listings = scrape_olx(page, model, storage, city)
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

    print(f"[{datetime.now()}] Robot starting...")
    notify_start()

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

                        count, err = run_olx(page, model, storage, city)
                        if err:
                            total_errors += 1
                            print(f"  OLX ERROR: {label} -> {err}")
                        elif count > 0:
                            total_ads += count
                            print(f"  OLX: {label} -> {count} ads")

        finally:
            browser.close()

    elapsed = time.time() - start_time
    summary = (
        f"[{datetime.now()}] Robot finished. "
        f"Ads: {total_ads}, Errors: {total_errors}, Time: {elapsed:.0f}s"
    )
    print(summary)

    try:
        notify_finish(total_ads, total_errors, elapsed)
    except Exception as e:
        print(f"  Telegram notification error: {e}")


if __name__ == '__main__':
    try:
        main()
    except Exception as e:
        error_msg = f"{type(e).__name__}: {str(e)[:200]}"
        print(f"FATAL ERROR: {error_msg}")
        print(traceback.format_exc())
        try:
            notify_error(error_msg)
        except Exception:
            pass
