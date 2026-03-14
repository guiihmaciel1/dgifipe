# DG iFipe Robot

Robô de coleta de anúncios de iPhone para o sistema DG iFipe — A tabela FIPE dos iPhones.

## Setup

```bash
cd /root/dgifipe/robots
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
playwright install chromium
```

## Configuração

Copie o `.env.example` para `.env` e configure:

```bash
cp .env.example .env
```

Preencha `TELEGRAM_BOT_TOKEN` e `TELEGRAM_CHAT_ID` para receber alertas.

## Execução Manual

```bash
source venv/bin/activate
python main.py
```

## Cron (execução diária às 03:00)

```cron
0 3 * * * cd /root/dgifipe/robots && /root/dgifipe/robots/venv/bin/python main.py >> /var/log/dgifipe/robot.log 2>&1
```

## Fontes

- **Facebook Marketplace**: Busca por modelo + storage na região
- **OLX**: Busca por modelo + storage em SP interior

## Logging

- Logs sumarizados (total de anúncios, erros, tempo)
- Alertas via Telegram no início e fim da coleta
- Erros críticos notificados via Telegram
