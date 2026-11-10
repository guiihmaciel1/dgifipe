# Execução do robô (macOS)

O robô roda na máquina local, e não no servidor: o Facebook bloqueia o acesso a partir do IP da VPS.

## Execução manual (padrão)

Dê dois cliques em **`Robo DG iFipe.command`**, na Área de Trabalho.

- Abre o Terminal e mostra o progresso da coleta (cerca de 13 minutos).
- Mantém o Mac acordado durante a coleta (`caffeinate`).
- Grava a saída também em `~/Library/Logs/dgifipe/robot.log`.
- No fim, mostra se terminou com sucesso ou com erro e espera uma tecla para fechar.

Requisitos: internet e a pasta `venv` configurada (veja "Configurar em outra máquina").

### Recriar o atalho

Crie `~/Desktop/Robo DG iFipe.command`, ajustando `ROBOTS_DIR`:

```bash
#!/bin/bash
ROBOTS_DIR="/CAMINHO/DO/PROJETO/robots"
LOG_DIR="$HOME/Library/Logs/dgifipe"
LOG_FILE="$LOG_DIR/robot.log"

mkdir -p "$LOG_DIR"
cd "$ROBOTS_DIR" || { echo "Pasta do robô não encontrada: $ROBOTS_DIR"; read -n 1 -s -r; exit 1; }

echo "=== Robô DG iFipe ==="
caffeinate -i venv/bin/python -u main.py 2>&1 | tee -a "$LOG_FILE"
STATUS=${PIPESTATUS[0]}

echo
[ "$STATUS" -eq 0 ] && echo "Concluído." || echo "Terminou com erro (código $STATUS)."
read -n 1 -s -r -p "Pressione qualquer tecla para fechar..."
```

E torne-o executável:

```bash
chmod +x ~/Desktop/"Robo DG iFipe.command"
```

### Rodar pelo terminal

```bash
cd robots
venv/bin/python main.py
```

## Ver o log

```bash
tail -f ~/Library/Logs/dgifipe/robot.log
```

## Configurar em outra máquina

Python 3.10+:

```bash
cd robots
python3 -m venv venv
venv/bin/pip install -r requirements.txt
venv/bin/playwright install chromium
cp .env.example .env   # preencha as credenciais do banco
```

## Agendamento automático (opcional, desativado)

Para voltar a rodar sozinho, use o launchd do macOS. Se o Mac estiver dormindo no horário, roda ao acordar.

1. Crie `~/Library/LaunchAgents/br.com.dgifipe.robot.plist` (exemplo: segunda e quinta às 09:00;
   `Weekday`: 0 ou 7 = domingo, 1 = segunda, ..., 6 = sábado):

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
            <key>Weekday</key><integer>1</integer>
            <key>Hour</key><integer>9</integer>
            <key>Minute</key><integer>0</integer>
        </dict>
        <dict>
            <key>Weekday</key><integer>4</integer>
            <key>Hour</key><integer>9</integer>
            <key>Minute</key><integer>0</integer>
        </dict>
    </array>
    <key>StandardOutPath</key>
    <string>/Users/SEU_USUARIO/Library/Logs/dgifipe/robot.log</string>
    <key>StandardErrorPath</key>
    <string>/Users/SEU_USUARIO/Library/Logs/dgifipe/robot.log</string>
</dict>
</plist>
```

2. Comandos:

```bash
# ativar
mkdir -p ~/Library/Logs/dgifipe
launchctl bootstrap gui/$(id -u) ~/Library/LaunchAgents/br.com.dgifipe.robot.plist

# rodar agora, sem esperar o horário
launchctl kickstart gui/$(id -u)/br.com.dgifipe.robot

# ver status (runs, last exit code)
launchctl print gui/$(id -u)/br.com.dgifipe.robot | grep -E "state|runs|last exit"

# desativar (remova também o .plist, senão volta no próximo login)
launchctl bootout gui/$(id -u)/br.com.dgifipe.robot
rm ~/Library/LaunchAgents/br.com.dgifipe.robot.plist
```
