import logging
import re
import time
import urllib.parse
from datetime import date
from playwright.sync_api import Page, TimeoutError as PlaywrightTimeout
from config import CITY_OLX_SLUGS, SEARCH_KEYWORDS_TEMPLATE, REQUEST_DELAY_SECONDS

logger = logging.getLogger(__name__)


def scrape_olx(page: Page, model: str, storage: str, city: str) -> list[dict]:
    """Scrape OLX for iPhone listings using a shared browser page."""
    region = CITY_OLX_SLUGS.get(city, 'regiao-de-sao-jose-do-rio-preto')
    query = SEARCH_KEYWORDS_TEMPLATE.format(model=model, storage=storage)
    encoded_query = urllib.parse.quote(query)

    search_url = f"https://www.olx.com.br/estado-sp/{region}?q={encoded_query}"
    listings = []

    try:
        page.goto(search_url, timeout=30000, wait_until='domcontentloaded')
        time.sleep(REQUEST_DELAY_SECONDS)

        listings = _extract_listings(page, model, storage, city)

    except PlaywrightTimeout:
        logger.warning("Timeout scraping OLX: %s %s - %s", model, storage, city)
    except Exception:
        raise

    return listings


def _extract_listings(page: Page, model: str, storage: str, city: str) -> list[dict]:
    """Extract listing data from OLX search results."""
    listings = []

    # OLX uses section links with parent class 'olx-adcard__topbody'
    # Find all <a> inside sections that contain price info
    ad_links = page.query_selector_all('section a')

    seen_urls = set()

    for link in ad_links[:50]:
        try:
            text = link.inner_text().strip()
            href = link.get_attribute('href') or ''

            if not href or not text or 'R$' not in text:
                continue

            if href in seen_urls:
                continue
            seen_urls.add(href)

            price = _extract_price(text)
            if not price or price < 500 or price > 15000:
                continue

            title = _extract_title(text)

            full_url = href if href.startswith('http') else f"https://www.olx.com.br{href}"

            listings.append({
                'model': model,
                'storage': storage,
                'price': price,
                'city': city,
                'source': 'olx',
                'title': title[:255],
                'url': full_url.split('?')[0],
                'collected_at': date.today().isoformat(),
            })
        except Exception:
            continue

    return listings


def _extract_price(text: str) -> float | None:
    """Extract the first R$ price from text."""
    text = text.replace('\xa0', ' ')
    match = re.search(r'R\$\s*([\d.]+(?:,\d{2})?)', text)
    if match:
        price_str = match.group(1).replace('.', '').replace(',', '.')
        try:
            return float(price_str)
        except ValueError:
            pass
    return None


def _extract_title(text: str) -> str:
    """Extract the title portion from the ad text (before the R$)."""
    idx = text.find('R$')
    if idx > 0:
        return text[:idx].strip().rstrip('/')
    lines = [l.strip() for l in text.split('\n') if l.strip()]
    return lines[0] if lines else ''
