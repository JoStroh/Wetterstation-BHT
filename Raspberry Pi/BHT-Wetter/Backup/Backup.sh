#!/bin/bash

# Festplatte ggf. aus falschem Verzeichnis auswerfen
sudo umount /media/pi/VOLUME
#Festplatte einbinden
#sudo  mount /dev/sda1 /home/BHT-Wetter/Backup/Data/

#Variablen
BACKUP_PFAD="/home/BHT-Wetter/Backup/Data"
BACKUP_ANZAHL="2"
BACKUP_NAME="BHT-Wetter"

#Alte Sicherung löschen
#pushd ${BACKUP_PFAD}; ls -tr ${BACKUP_PFAD}/${BACKUP_NAME}* | head -n -${BACKUP_ANZAHL} | xargs rm; popd

#Backup erstellen
#sudo dd if=/dev/mmcblk0 |gzip -5 >${BACKUP_PFAD}/${BACKUP_NAME}-$(date +%Y%m%d).img.gz

#Festplatte auswerfen
sudo umount /home/BHT-Wetter/Backup/Data