# Agendamento local do robô (macOS)

O robô roda na máquina local, e não no servidor: o Facebook bloqueia o acesso a partir do IP da VPS.
O agendamento usa o **launchd** do macOS.

## Como está configurado

- **Quando:** segunda e quinta às 09:00. Se o Mac estiver dormindo no horário, roda assim que ele acordar. Se estiver desligado, a execução é perdida.
- **O que roda:** `venv/bin/python -u main.py`, dentro desta pasta, usando o `.env` local.
- **Agendamento:** `~/Library/LaunchAgents/br.com.dgifipe.robot.plist`
- **Log:** `~/Library/Logs/dgifipe/robot.log`

Requisitos: Mac ligado, sessão do usuário aberta e internet. Uma coleta leva cerca de 13 minutos.

## Comandos

```bash
# ver o log
tail -f ~/Library/Logs/dgifipe/robot.log

# rodar agora, sem esperar o horário
launchctl kickstart gui/$(id -u)/br.com.dgifipe.robot

# ver status (runs, last exit code)
launchctl print gui/$(id -u)/br.com.dgifipe.robot | grep -E "state|runs|last exit"

# desativar o agendamento
launchctl bootout gui/$(id -u)/br.com.dgifipe.robot

# reativar (depois de um bootout ou de editar o .plist)
launchctl bootstrap gui/$(id -u) ~/Library/LaunchAgents/br.com.dgifipe.robot.plist
```

## Alterar dias e horário

1. Edite os blocos `Weekday`, `Hour` e `Minute` em `~/Library/LaunchAgents/br.com.dgifipe.robot.plist`
   (`Weekday`: 0 ou 7 = domingo, 1 = segunda, ..., 6 = sábado).
2. Recarregue:

```bash
launchctl bootout gui/$(id -u)/br.com.dgifipe.robot
launchctl bootstrap gui/$(id -u) ~/Library/LaunchAgents/br.com.dgifipe.robot.plist
```

## Configurar em outra máquina

1. Prepare o ambiente (Python 3.10+):

```bash
cd robots
python3 -m venv venv
venv/bin/pip install -r requirements.txt
venv/bin/playwright install chromium
cp .env.example .env   # preencha as credenciais do banco
mkdir -p ~/Library/Logs/dgifipe
```

2. Crie `~/Library/LaunchAgents/br.com.dgifipe.robot.plist`, ajustando os caminhos para a pasta do projeto:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>Label</key>
    <string>br.com.dgifipe.robot</string>

    <key>ProgramArguments</key>
    <array>
        <string>/CAMINHO/DO/PROJETO/robots/venv/bin/python</string>
        <string>-u</string>
        <string>main.py</string>
    </array>

    <key>WorkingDirectory</key>
    <string>/CAMINHO/DO/PROJETO/robots</string>

    <key>StartCalendarInterval</key>
    <array>
        <dict>
            <key>Weekday</key>
            <integer>1</integer>
            <key>Hour</key>
            <integer>9</integer>
            <key>Minute</key>
            <integer>0</integer>
        </dict>
        <dict>
            <key>Weekday</key>
            <integer>4</integer>
            <key>Hour</key>
            <integer>9</integer>
            <key>Minute</key>
            <integer>0</integer>
        </dict>
    </array>

    <key>StandardOutPath</key>
    <string>/Users/SEU_USUARIO/Library/Logs/dgifipe/robot.log</string>
    <key>StandardErrorPath</key>
    <string>/Users/SEU_USUARIO/Library/Logs/dgifipe/robot.log</string>
</dict>
</plist>
```

3. Ative:

```bash
launchctl bootstrap gui/$(id -u) ~/Library/LaunchAgents/br.com.dgifipe.robot.plist
```
