import re
from config import MODELS

SKIP_KEYWORDS = re.compile(
    r'\b(lacrado|lacrada|selado|selada|sealed|new & sealed|novo na caixa|zero na caixa)\b',
    re.IGNORECASE,
)

NOT_IPHONE = re.compile(
    r'\b(ipad|macbook|mac book|apple watch|airpods|imac|mac mini|mac pro|mac studio|apple tv|homepod)\b',
    re.IGNORECASE,
)

STORAGES = ['1tb', '512gb', '256gb', '128gb', '64gb']

_MODEL_NAMES = sorted(MODELS.keys(), key=len, reverse=True)

_MODEL_PATTERNS: list[tuple[re.Pattern, str]] = []
for _name in _MODEL_NAMES:
    _lower = _name.lower()
    _escaped = re.escape(_lower).replace(r'\ ', r' ?')
    _MODEL_PATTERNS.append((re.compile(_escaped), _lower))

_STORAGE_PATTERNS: list[tuple[re.Pattern, str]] = []
for _s in STORAGES:
    _num = _s.rstrip('gbt')
    _unit = _s[len(_num):]
    _STORAGE_PATTERNS.append((re.compile(rf'{_num}\s?{_unit}'), _s))


def _detect_model(title: str) -> str | None:
    for pattern, value in _MODEL_PATTERNS:
        if pattern.search(title):
            return value
    return None


def _detect_storage(title: str) -> str | None:
    for pattern, value in _STORAGE_PATTERNS:
        if pattern.search(title):
            return value
    return None


def _is_contradictory(title: str, model: str, storage: str) -> bool:
    if not title or 'iphone' not in title.lower():
        return False

    lower_title = title.lower()

    detected_model = _detect_model(lower_title)
    if detected_model and detected_model != model.lower():
        return True

    detected_storage = _detect_storage(lower_title)
    if detected_storage and detected_storage != storage.lower():
        return True

    return False


def should_skip(listing: dict) -> bool:
    title = listing.get('title', '')
    if not title:
        return False
    if SKIP_KEYWORDS.search(title):
        return True
    if NOT_IPHONE.search(title):
        return True
    return _is_contradictory(title, listing['model'], listing['storage'])
