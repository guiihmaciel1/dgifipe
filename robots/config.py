import os
from dotenv import load_dotenv

load_dotenv()

DB_CONFIG = {
    'host': os.getenv('DB_HOST', '127.0.0.1'),
    'port': int(os.getenv('DB_PORT', 3306)),
    'database': os.getenv('DB_DATABASE', 'dgifipe'),
    'user': os.getenv('DB_USERNAME', 'dgifipe'),
    'password': os.getenv('DB_PASSWORD', ''),
}

TELEGRAM_BOT_TOKEN = os.getenv('TELEGRAM_BOT_TOKEN', '')
TELEGRAM_CHAT_ID = os.getenv('TELEGRAM_CHAT_ID', '')

CITIES = [
    'São José do Rio Preto',
    'Mirassol',
    'Santa Fé do Sul',
]

CITY_FB_SLUGS = {
    'São José do Rio Preto': 'sao-jose-do-rio-preto',
    'Mirassol': 'mirassol',
    'Santa Fé do Sul': 'santa-fe-do-sul',
}

CITY_OLX_SLUGS = {
    'São José do Rio Preto': 'sao-jose-do-rio-preto-e-regiao',
    'Mirassol': 'sao-jose-do-rio-preto-e-regiao',
    'Santa Fé do Sul': 'sao-jose-do-rio-preto-e-regiao',
}

MODELS = {
    'iPhone 11':         ['64GB', '128GB', '256GB'],
    'iPhone 11 Pro':     ['64GB', '256GB', '512GB'],
    'iPhone 11 Pro Max': ['64GB', '256GB', '512GB'],
    'iPhone 12 mini':    ['64GB', '128GB', '256GB'],
    'iPhone 12':         ['64GB', '128GB', '256GB'],
    'iPhone 12 Pro':     ['128GB', '256GB', '512GB'],
    'iPhone 12 Pro Max': ['128GB', '256GB', '512GB'],
    'iPhone 13 mini':    ['128GB', '256GB', '512GB'],
    'iPhone 13':         ['128GB', '256GB', '512GB'],
    'iPhone 13 Pro':     ['128GB', '256GB', '512GB', '1TB'],
    'iPhone 13 Pro Max': ['128GB', '256GB', '512GB', '1TB'],
    'iPhone 14':         ['128GB', '256GB', '512GB'],
    'iPhone 14 Plus':    ['128GB', '256GB', '512GB'],
    'iPhone 14 Pro':     ['128GB', '256GB', '512GB', '1TB'],
    'iPhone 14 Pro Max': ['128GB', '256GB', '512GB', '1TB'],
    'iPhone 15':         ['128GB', '256GB', '512GB'],
    'iPhone 15 Plus':    ['128GB', '256GB', '512GB'],
    'iPhone 15 Pro':     ['128GB', '256GB', '512GB', '1TB'],
    'iPhone 15 Pro Max': ['256GB', '512GB', '1TB'],
    'iPhone 16':         ['128GB', '256GB', '512GB'],
    'iPhone 16 Plus':    ['128GB', '256GB', '512GB'],
    'iPhone 16 Pro':     ['128GB', '256GB', '512GB', '1TB'],
    'iPhone 16 Pro Max': ['256GB', '512GB', '1TB'],
    'iPhone 16e':        ['128GB', '256GB', '512GB'],
    'iPhone 17':         ['128GB', '256GB', '512GB'],
    'iPhone 17 Air':     ['128GB', '256GB', '512GB'],
    'iPhone 17 Pro':     ['128GB', '256GB', '512GB', '1TB'],
    'iPhone 17 Pro Max': ['256GB', '512GB', '1TB'],
}

SEARCH_KEYWORDS_TEMPLATE = "{model} {storage}"

HEADLESS = True

MAX_SCROLL_ATTEMPTS = 3

REQUEST_DELAY_SECONDS = 2

USER_AGENT = (
    'Mozilla/5.0 (Windows NT 10.0; Win64; x64) '
    'AppleWebKit/537.36 (KHTML, like Gecko) '
    'Chrome/131.0.0.0 Safari/537.36'
)
