# Sauvetage-Ouchy Kiosk

Le kiosk est un display allumé en permanance affichant differentes informations liées au sauvetage et permettant des saisie simple via un touche screen.
différentes pages peuvent être créées et seront affichée en rotation 

Ce folder contient les differentes pages à afficher

# Spécifications techniques
- Raspberry PI 5, OS minimal, Chromium en mode Kiosk
- Display medium 800x1280 
- Display large 1920x1080 


# Pages 

## to do 


## Pages en cours  
- Welcome : Affiche un écran de Bienvenue au Sauvetage d'Ouchy 
  ```js
   {
        "url": "/kiosk/pages/vitrine019_kiosk.php",
        "seconds": 10,
        "button": "Ouchy"
    }
  ```
- Webcam : Affiche les dernières images  de la webcam 
  ```js
        {
        "url": "/kiosk/pages/webcam_kiosk.php",
        "seconds": 10,
        "button": "Webcam"
      }
  ```
 - Météo
   ```js
   {
        "url": "/kiosk/pages/meteo_kiosk.php",
        "seconds": 10,
        "button": "Météo"
      },
        ```
- Calendrier :  Affiche un calendrier minimal avec infomations des events et vigies à venir
  ```js
        {
        "url": "/kiosk/pages/calendrier_kiosk.php",
        "seconds": 10,
        "button": "Calendrier"
      }
  ```
- SOS  : Affiche la carte SOS et localisation 
  ```js
        {
        "url": "https://map.sauvetage-ouchy.ch/?mode=kiosk",
        "seconds": 10,
        "button": "SOS Localisation"
      } 
  ``` 
- Assemblée Générale 2026 
  ```js
 {
        "url": "/kiosk/pages/AG_2026.php",
        "seconds": 10,
        "button": "AG 2026"
      },
   ``` 

## Pages disponible 
- SISL-J.Motorisée 2025
  ```js
      {
        "url": "/kiosk/pages/journeemotorisee_event_kiosk.php",
        "seconds": 10,
        "button": "Journée Mot."
    }

  ```
-  Radios et carte SISL : Affiche la liste des sauvetage actifs 
  ```js
       {
        "url": "/kiosk/pages/radiosactives_kiosk.php",
        "seconds": 10,
        "button": "Radios"
      }
  ```

## autres idée 
- Mode emploi radio : dans page radio, pup-up ? 
- capteur RFID badge, et auto login, puis carnet de bors ou liste des vigie ou edit personnal info 

# config PI 

## connection ssh
ssh pi@192.168.1.32

## Update OS
To update the OS after initial flash 
comnnect via terminal
```zsh
sudo apt update        # to refresh the package list
sudo apt full-upgrade   # to install all available updates.
sudo reboot   # to reboot

sudo apt install fonts-noto-color-emoji # to install emoji font for kioak for instance

fc-cache -f -v # (optionnel) reconstruire le cache des polices
```




## Lancement manuel


RPI5 full 
````zsh
/usr/bin/chromium 
  --kiosk --start-fullscreen \
  --no-first-run --no-default-browser-check \
  --disable-features=Translate \
  --ozone-platform=wayland https://sauvetage-ouchy.ch/kiosk/kiosk.php  &
```


## config pour un kiosk 

Crée /etc/chromium/policies/managed/kiosk.json avec :
{
  "TranslateEnabled": false,
  "DefaultBrowserSettingEnabled": false
}

## commendes type

Suppression des processus chromium 
````zsh
pgrep chromium
sudo kill -9 [ID_PROCESSUS]
````

## Creer un fichier service pour demarage automatique 
```zsh
sudo tee /etc/systemd/system/kiosk.service >/dev/null <<'EOF'
# /etc/systemd/system/kiosk.service
[Unit]
Description=Chromium Kiosk
After=graphical.target network-online.target systemd-time-wait-sync.service
Wants=network-online.target systemd-time-wait-sync.service

[Service]
# Les commandes *StartPre* tourneront en root (pour écrire dans /sys)
PermissionsStartOnly=true
# Optionnel: patienter explicitement jusqu'à la synchro NTP
ExecStartPre=/bin/sh -c 'for i in $(seq 1 120); do \
  [ -e /run/systemd/timesync/synchronized ] && exit 0; sleep 1; done; exit 0'
# Régler la luminosité avant de lancer Chromium
ExecStartPre=/bin/sh -c 'echo 160 > /sys/class/backlight/11-0045/brightness'
# lancer chrome avec rpi
User=pi
Environment=WAYLAND_DISPLAY=wayland-0
Environment=XDG_RUNTIME_DIR=/run/user/1000
ExecStart=/bin/bash -lc 'chromium --kiosk --start-fullscreen --no-first-run --no-default-browser-check --disable-features=Translate --ozone-platform=wayland  https://sauvetage-ouchy.ch/kiosk/kiosk.php?deviceID=1AC2F8D0F901'
Restart=always
RestartSec=3

[Install]
WantedBy=graphical.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable kiosk.service
sudo systemctl start kiosk.service

sudo systemctl stop kiosk.service

```
# Brancher le display

Instructions : 
https://www.waveshare.com/wiki/13.3inch_DSI_LCD 

## reglage de la luminosité
```zsh
# le chemin pour le display 13'3 pouces
BL=/sys/class/backlight/11-0045
# max et min possible 
cat $BL/brightness
cat $BL/max_brightness
102
255
pi@pi:~ $ echo 250 | sudo tee /sys/class/backlight/11-0045/brightness
```

## Gérer les connection réseau

```zsh
    # list of configured connection-name 
    sudo nmcli c show
    nmcli -f NAME,TYPE,AUTOCONNECT,AUTOCONNECT-PRIORITY connection
    #V iew the attributes for a connection:
    sudo nmcli c show <connection-name>
    # Bring a network connection down and up
      sudo nmcli c down <connection-name>
      sudo nmcli c up <connection-name>
    # Modifiy an attribut
    sudo nmcli c modify <connection-name> attribute value
    # Rename a connection:
      sudo nmcli c mod <connection-name> connection.id <new-connection-name>
```
Configurer une connection de secours

```zsh
# Wi-Fi prioritaire
nmcli dev wifi connect "FiberBox_X6_3434A7" password "j0r4tf1x3" ifname wlan0
nmcli connection modify "FiberBox_X6_3434A7" connection.autoconnect yes connection.autoconnect-priority 10

# Wi-Fi de secours
nmcli dev wifi connect "SSID_SEC" password "motdepasse2" ifname wlan0
nmcli connection modify "SSID_SEC" connection.autoconnect yes connection.autoconnect-priority 0

```
Crée un profil Wi-Fi "primaire" (remplace SSID/PSK)
```zsh
sudo nmcli connection add type wifi ifname wlan0 con-name WIFI_PRI ssid "FiberBox_X6_3434A7"
sudo nmcli connection modify WIFI_PRI  wifi-sec.key-mgmt wpa-psk wifi-sec.psk "j0r4tf1x3"  connection.autoconnect yes connection.autoconnect-priority 10
```

# commection via ethernet ente mac et Rpi
1 mettre mac ethernet en 192.168.2.1
2 configurer rpi
```zsh
sudo nmcli connection modify "Wired connection 1" \
  ipv4.method manual \
  ipv4.addresses 192.168.2.2/24 \
  ipv4.gateway "" \
  ipv4.dns "" \
  connection.autoconnect yes

sudo nmcli connection down "Wired connection 1" && sudo nmcli connection up "Wired connection 1"
ip addr show dev eth0
```


