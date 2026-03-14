import asyncio
from telegram import Bot
from config import TELEGRAM_BOT_TOKEN, TELEGRAM_CHAT_ID


def send_message(text: str) -> None:
    """Send a message to the configured Telegram chat."""
    if not TELEGRAM_BOT_TOKEN or not TELEGRAM_CHAT_ID:
        print(f"[Telegram disabled] {text}")
        return

    try:
        bot = Bot(token=TELEGRAM_BOT_TOKEN)
        asyncio.get_event_loop().run_until_complete(
            bot.send_message(chat_id=TELEGRAM_CHAT_ID, text=text, parse_mode='HTML')
        )
    except RuntimeError:
        loop = asyncio.new_event_loop()
        asyncio.set_event_loop(loop)
        loop.run_until_complete(
            Bot(token=TELEGRAM_BOT_TOKEN).send_message(
                chat_id=TELEGRAM_CHAT_ID, text=text, parse_mode='HTML'
            )
        )
    except Exception as e:
        print(f"[Telegram error] {e}")


def notify_start():
    send_message("🤖 <b>DG iFipe Robot</b>\n\n✅ Coleta iniciada.")


def notify_finish(total_ads: int, errors: int, elapsed_seconds: float):
    minutes = elapsed_seconds / 60
    send_message(
        f"🤖 <b>DG iFipe Robot</b>\n\n"
        f"✅ Coleta finalizada.\n"
        f"📊 Anúncios: <b>{total_ads}</b>\n"
        f"❌ Erros: <b>{errors}</b>\n"
        f"⏱ Tempo: <b>{minutes:.1f} min</b>"
    )


def notify_error(error_msg: str):
    send_message(f"🤖 <b>DG iFipe Robot</b>\n\n❌ Erro: {error_msg}")
