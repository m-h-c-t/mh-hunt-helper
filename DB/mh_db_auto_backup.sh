#!/bin/bash
echo "===== started mh_db_auto_backup_weekly.sh ======"
source /var/www/mh-hunt-helper/DB/config.sh

#cd /keybase/public/devjacksmith/mh_backups/weekly

# Hunt Helper
echo "====== Backing up hunthelper ====="

if [ -f hunthelper_weekly.sql.gz ]; then
    rm hunthelper_weekly.sql.gz
fi

if [ -f hunthelper_weekly.txt.zip ]; then
    rm hunthelper_weekly.txt.zip
fi

echo "=== Turning off event scheduler ==="
mysql -u $MH_USER -p$MH_PASS -e "SET GLOBAL event_scheduler = OFF;"

mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events --routines mhhunthelper | gzip -9 > hunthelper_weekly.sql.gz
sleep 5s
rm -rf /var/lib/mysql-files/*
mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events --routines -T /var/lib/mysql-files/ --no-create-info --compatible=ansi mhhunthelper
rm -rf /var/lib/mysql-files/*.sql
zip -j -9 hunthelper_weekly.txt.zip /var/lib/mysql-files/*
rm -rf /var/lib/mysql-files/*

# Map Spotter
echo "===== Backing up map spotter ====="

if [ -f mapspotter_weekly.sql.gz ]; then
    rm mapspotter_weekly.sql.gz
fi

if [ -f mapspotter_weekly.txt.zip ]; then
        rm mapspotter_weekly.txt.zip
fi

mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events --routines mhmapspotter | gzip -9 > mapspotter_weekly.sql.gz
sleep 5s
rm -rf /var/lib/mysql-files/*
mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events --routines -T /var/lib/mysql-files/ --no-create-info --compatible=ansi mhmapspotter
rm -rf /var/lib/mysql-files/*.sql
zip -j -9 mapspotter_weekly.txt.zip /var/lib/mysql-files/*
rm -rf /var/lib/mysql-files/*


# Converter
echo "===== Backing up converter ====="

if [ -f converter_weekly.sql.gz ]; then
    rm converter_weekly.sql.gz
fi

if [ -f converter_weekly.txt.zip ]; then
    rm converter_weekly.txt.zip
fi

mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events --routines mhconverter | gzip -9 > converter_weekly.sql.gz
sleep 5s
rm -rf /var/lib/mysql-files/*
mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events --routines -T /var/lib/mysql-files/ --no-create-info --compatible=ansi mhconverter
rm -rf /var/lib/mysql-files/*.sql
zip -j -9 converter_weekly.txt.zip /var/lib/mysql-files/*
rm -rf /var/lib/mysql-files/*

echo "=== Turning on event scheduler ==="
mysql -u $MH_USER -p$MH_PASS -e "SET GLOBAL event_scheduler = ON;"

date > last_updated.txt

echo "===== Copying to keybase ====="

su user -c 'cp converter_weekly.sql.gz converter_weekly.txt.zip mapspotter_weekly.sql.gz mapspotter_weekly.txt.zip hunthelper_weekly.sql.gz hunthelper_weekly.txt.zip last_updated.txt  /keybase/public/devjacksmith/mh_backups/weekly/'
su user -c 'cp converter_weekly.sql.gz converter_weekly.txt.zip mapspotter_weekly.sql.gz mapspotter_weekly.txt.zip hunthelper_weekly.sql.gz hunthelper_weekly.txt.zip last_updated.txt  /backups/weekly/'

rm -rf *.sql.gz *.txt.zip

echo "===== Remote trigger Docker image builds ====="
echo

curl -H "Content-Type: application/json" --data '{"source_type": "converter", "source_name": "converter"}' -X POST $DOCKER_CURL
echo
echo "= converter_weekly triggered ="
echo
sleep 2s

curl -H "Content-Type: application/json" --data '{"source_type": "mapspotter", "source_name": "mapspotter"}' -X POST $DOCKER_CURL
echo
echo "= mapspotter_weekly triggered ="
echo
sleep 2s

curl -H "Content-Type: application/json" --data '{"source_type": "weekly", "source_name": "weekly"}' -X POST $DOCKER_CURL
echo
echo "= hunthelper_weekly triggered ="
echo

echo "===== finished mh_db_auto_backup_weekly.sh ====="
