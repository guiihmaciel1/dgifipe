#!/usr/bin/env python3
"""
Quick test run — limited to a few popular models to validate the full pipeline.
"""
import time
import traceback
from datetime import datetime

from playwright.sync_api import sync_playwright

from config import MODELS, CITIES, HEADLESS, USER_AGENT
from database import insert_listings, cleanup_old_listings
from scraper_facebook import scrape_facebook
from scraper_olx import scrape_olx


def create_browser_page(playwright):
    browser = playwright.chromium.launch(headless=HEADLESS)
    context = browser.new_context(
        locale='pt-BR',
        user_agent=USER_AGENT,
        viewport={'width': 1366, 'height': 768},
    )
    page = context.new_page()
    return browser, page


def main():
    start = time.time()
    total_ads = 0
    total_errors = 0

    print(f"[{datetime.now()}] Test run starting...")
    print(f"  Models: {len(MODELS)} modelos")
    print(f"  Cities: {CITIES}")
    print()

    with sync_playwright() as p:
        browser, page = create_browser_page(p)

        try:
            for model, storages in MODELS.items():
                for storage in storages:
                    for city in CITIES:
                        label = f"{model} {storage} - {city}"

                        # Facebook
                        print(f"  [FB] {label}...", end=" ", flush=True)
                        try:
                            listings = scrape_facebook(page, model, storage, city)
                            if listings:
                                count = insert_listings(listings)
                                total_ads += count
                                print(f"OK -> {count} ads")
                            else:
                                print("0 ads")
                        except Exception as e:
                            total_errors += 1
                            print(f"ERROR: {e}")

                        # OLX
                        print(f"  [OLX] {label}...", end=" ", flush=True)
                        try:
                            listings = scrape_olx(page, model, storage, city)
                            if listings:
                                count = insert_listings(listings)
                                total_ads += count
                                print(f"OK -> {count} ads")
                            else:
                                print("0 ads")
                        except Exception as e:
                            total_errors += 1
                            print(f"ERROR: {e}")

        finally:
            browser.close()

    elapsed = time.time() - start
    print(f"\n[{datetime.now()}] Done. Ads: {total_ads}, Errors: {total_errors}, Time: {elapsed:.0f}s")


if __name__ == '__main__':
    try:
        main()
    except Exception as e:
        print(f"FATAL: {e}")
        traceback.print_exc()
