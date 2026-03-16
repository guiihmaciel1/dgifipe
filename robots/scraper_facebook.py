import logging
import re
import time
import urllib.parse
from datetime import date
from playwright.sync_api import Page, TimeoutError as PlaywrightTimeout
from config import (
    CITY_FB_IDS,
    FB_RADIUS_KM,
    SEARCH_KEYWORDS_TEMPLATE,
    MAX_SCROLL_ATTEMPTS,
    REQUEST_DELAY_SECONDS,
)

logger = logging.getLogger(__name__)


def scrape_facebook(page: Page, model: str, storage: str, city: str) -> list[dict]:
    """
    Scrape Facebook Marketplace for iPhone listings.
    Uses city ID + radius URL format for accurate geolocation.
    """
    city_id = CITY_FB_IDS.get(city, '108568625834990')
    query = SEARCH_KEYWORDS_TEMPLATE.format(model=model, storage=storage)
    encoded_query = urllib.parse.quote(query)

    search_url = (
        f"https://www.facebook.com/marketplace/{city_id}/search/"
        f"?query={encoded_query}&exact=false"
        f"&radius_in_km={FB_RADIUS_KM}&locale=pt_BR"
    )

    listings = []

    try:
        page.goto(search_url, timeout=30000, wait_until='domcontentloaded')
        time.sleep(REQUEST_DELAY_SECONDS)

        _dismiss_login_modal(page)

        _scroll_to_load(page)

        listings = _extract_listings(page, model, storage, city)

    except PlaywrightTimeout:
        logger.warning("Timeout scraping Facebook: %s %s - %s", model, storage, city)
    except Exception:
        raise

    return listings


def _dismiss_login_modal(page: Page) -> None:
    """Close the Facebook login modal if it appears."""
    close_selectors = [
        '[aria-label="Fechar"]',
        '[aria-label="Close"]',
        'div[role="dialog"] div[aria-label="Fechar"]',
        'div[role="dialog"] div[aria-label="Close"]',
        'div[role="dialog"] [role="button"]:has(svg)',
    ]

    for attempt in range(3):
        try:
            for selector in close_selectors:
                btn = page.query_selector(selector)
                if btn and btn.is_visible():
                    btn.click()
                    time.sleep(0.5)
                    return

            page.keyboard.press('Escape')
            time.sleep(0.5)

            if not page.query_selector('div[role="dialog"]'):
                return

        except Exception:
            pass

        time.sleep(1)


def _scroll_to_load(page: Page) -> None:
    """Scroll the page to trigger lazy loading of more listings."""
    for _ in range(MAX_SCROLL_ATTEMPTS):
        page.evaluate('window.scrollTo(0, document.body.scrollHeight)')
        time.sleep(1.5)


def _extract_listings(page: Page, model: str, storage: str, city: str) -> list[dict]:
    """Extract listing data from search results."""
    listings = []

    item_links = page.query_selector_all('a[href*="/marketplace/item/"]')

    seen_urls = set()

    for link in item_links:
        try:
            href = link.get_attribute('href') or ''
            if not href or href in seen_urls:
                continue
            seen_urls.add(href)

            full_text = link.inner_text().strip()
            if not full_text:
                continue

            lines = [l.strip() for l in full_text.split('\n') if l.strip()]
            if len(lines) < 2:
                continue

            price = _find_price_in_lines(lines)
            if not price or price < 500 or price > 15000:
                continue

            all_text = ' '.join(lines).lower()
            if 'iphone' not in all_text:
                continue

            title = _find_title_in_lines(lines)

            full_url = href
            if not href.startswith('http'):
                full_url = f"https://www.facebook.com{href}"

            listings.append({
                'model': model,
                'storage': storage,
                'price': price,
                'city': city,
                'source': 'facebook',
                'title': title[:255] if title else '',
                'url': full_url.split('?')[0],
                'collected_at': date.today().isoformat(),
            })

        except Exception:
            continue

    return listings


def _find_price_in_lines(lines: list[str]) -> float | None:
    """Find the first valid BRL price in the text lines."""
    for line in lines:
        price = parse_price(line)
        if price:
            return price
    return None


def _find_title_in_lines(lines: list[str]) -> str:
    """Find the title line — typically the line after the price."""
    for i, line in enumerate(lines):
        if parse_price(line) is not None:
            if i + 1 < len(lines):
                candidate = lines[i + 1]
                if parse_price(candidate) is None:
                    return candidate
            break

    for line in lines:
        if parse_price(line) is None and len(line) > 5:
            return line

    return ''


def parse_price(text: str) -> float | None:
    """
    Parse BRL prices from Facebook Marketplace.
    Handles: "R$5.500", "R$ 2.899", "R$3.699,00", "GRÁTIS"
    Also handles US$ for servers with American IP.
    """
    text = text.replace('\xa0', ' ').strip()

    if 'grátis' in text.lower() or 'gratuito' in text.lower():
        return None

    brl_match = re.search(r'R\$\s*([\d.]+(?:,\d{2})?)', text)
    if brl_match:
        price_str = brl_match.group(1).replace('.', '').replace(',', '.')
        try:
            return float(price_str)
        except ValueError:
            pass

    usd_match = re.search(r'US\$\s*([\d,]+(?:\.\d{2})?)', text)
    if usd_match:
        price_str = usd_match.group(1).replace(',', '')
        try:
            usd_val = float(price_str)
            return round(usd_val * 5.5, 2)
        except ValueError:
            pass

    return None
