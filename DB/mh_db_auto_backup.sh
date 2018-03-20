#!/bin/bash
echo "===== started mh_db_auto_backup.sh ======"
source /var/www/mh-hunt-helper/DB/config.sh

cd /keybase/public/devjacksmith/mh_backups/weekly
date > last_updated.txt

# Hunt Helper
echo "====== Backing up hunt helper ====="

if [ -f hunthelper_weekly.sql.gz ]; then
	rm hunthelper_weekly.sql.gz
fi

if [ -f hunthelper_weekly_txt.zip ]; then
	rm hunthelper_weekly_txt.zip
fi

mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events mhhunthelper | gzip -9 > hunthelper_weekly.sql.gz
sleep 5s
rm -rf /var/lib/mysql-files/*
mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables -T /var/lib/mysql-files/ --no-create-info --compatible=db2 mhhunthelper
rm -rf /var/lib/mysql-files/*.sql
zip -j -9 hunthelper_weekly.txt.zip /var/lib/mysql-files/*
rm -rf /var/lib/mysql-files/*

# Map Spotter
echo "===== Backing up map spotter ====="

if [ -f mapspotter_structure_weekly.sql.gz ]; then
	rm mapspotter_structure_weekly.sql.gz
fi

mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --no-data --events mhmapspotter | gzip -9 > mapspotter_structure_weekly.sql.gz

# Map Spotter
echo "===== Backing up map spotter ====="

if [ -f mapspotter_structure_weekly.sql.gz ]; then
	rm mapspotter_structure_weekly.sql.gz
fi

mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --no-data --events mhmapspotter | gzip -9 > mapspotter_structure_weekly.sql.gz

# Converter
echo "===== Backing up converter ====="

if [ -f converter_structure_weekly.sql.gz ]; then
	rm converter_structure_weekly.sql.gz
fi

mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events mhconverter | gzip -9 > converter_weekly.sql.gz


# Map Helper
echo "===== Backing up map helper ====="

if [ -f maphelper_weekly.sql.gz ]; then
	rm maphelper_weekly.sql.gz
fi

mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events mhmaphelper --ignore-table=mhmaphelper.users | gzip -9 > maphelper_weekly.sql.gz

echo "===== finished mh_db_auto_backup.sh ====="
