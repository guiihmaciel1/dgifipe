import re
import time
import urllib.parse
from datetime import date
from playwright.sync_api import Page, TimeoutError as PlaywrightTimeout
from config import CITY_OLX_SLUGS, SEARCH_KEYWORDS_TEMPLATE, REQUEST_DELAY_SECONDS


def scrape_olx(page: Page, model: str, storage: str, city: str) -> list[dict]:
    """
    Scrape OLX for iPhone listings using a shared browser page.
    """
    region = CITY_OLX_SLUGS.get(city, 'sao-jose-do-rio-preto-e-regiao')
    query = SEARCH_KEYWORDS_TEMPLATE.format(model=model, storage=storage)
    encoded_query = urllib.parse.quote(query)

    search_url = f"https://www.olx.com.br/celulares/iphone/estado-sp/{region}?q={encoded_query}"
    listings = []

    try:
        page.goto(search_url, timeout=30000, wait_until='domcontentloaded')
        time.sleep(REQUEST_DELAY_SECONDS)

        listings = _extract_listings(page, model, storage, city)

    except PlaywrightTimeout:
        pass
    except Exception:
        raise

    return listings


def _extract_listings(page: Page, model: str, storage: str, city: str) -> list[dict]:
    """Extract listing data from OLX search results."""
    listings = []

    items = page.query_selector_all('[data-ds-component="DS-AdCard"]')
    if not items:
        items = page.query_selector_all('a[data-lurker-detail]')

    for item in items[:50]:
        try:
            title_el = item.query_selector('h2') or item.query_selector('[class*="title"]')
            price_el = item.query_selector('[class*="price"]') or item.query_selector('span')

            if not price_el:
                continue

            title = title_el.inner_text().strip() if title_el else ''
            price_text = price_el.inner_text().strip()

            href = item.get_attribute('href') or ''
            if not href:
                link_el = item.query_selector('a')
                href = link_el.get_attribute('href') if link_el else ''

            price = parse_price(price_text)
            if not price or price < 500 or price > 15000:
                continue

            listings.append({
                'model': model,
                'storage': storage,
                'price': price,
                'city': city,
                'source': 'olx',
                'title': title[:255],
                'url': href if href.startswith('http') else f"https://www.olx.com.br{href}",
                'collected_at': date.today().isoformat(),
            })
        except Exception:
            continue

    return listings


def parse_price(text: str) -> float | None:
    """Parse BRL prices from OLX format like 'R$ 2.500'."""
    text = text.replace('\xa0', ' ').strip()

    match = re.search(r'R\$\s*([\d.]+(?:,\d{2})?)', text)
    if match:
        price_str = match.group(1).replace('.', '').replace(',', '.')
        try:
            return float(price_str)
        except ValueError:
            pass

    digits = re.sub(r'[^\d]', '', text)
    if digits:
        val = float(digits)
        if 500 <= val <= 15000:
            return val

    return None
