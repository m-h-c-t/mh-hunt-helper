#!/bin/bash
echo "===== started mh_db_auto_backup_nightly.sh ======"

source /var/www/mh-hunt-helper/DB/config.sh
#cd /keybase/public/devjacksmith/mh_backups/nightly

# Hunt Helper
echo "====== Backing up hunt helper ====="
echo "=== Removing old files ==="
if [ -f hunthelper_nightly.sql.gz ]; then
    rm hunthelper_nightly.sql.gz
fi

if [ -f hunthelper_nightly.txt.zip ]; then
    rm hunthelper_nightly.txt.zip
fi

echo "=== Turning off even scheduler ==="
mysql -u $MH_USER -p$MH_PASS -e "SET GLOBAL event_scheduler = OFF;"

echo "=== Dumping into sql.gz file ==="
mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events --routines mhhunthelper | gzip -9 > hunthelper_nightly.sql.gz
sleep 5s

echo "=== Dumping into txt files ==="
rm -rf $MH_DUMP/*
mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events --routines -T $MH_DUMP --no-create-info --compatible=db2 mhhunthelper

echo "=== Turning on even scheduler ==="
mysql -u $MH_USER -p$MH_PASS -e "SET GLOBAL event_scheduler = ON;"

echo "=== Zipping txt files ==="
rm -rf $MH_DUMP/*.sql
zip -j -9 hunthelper_nightly.txt.zip $MH_DUMP/*
rm -rf $MH_DUMP/*

echo "===== Copying files ====="

date > last_updated.txt

su user -c 'cp hunthelper_nightly.sql.gz hunthelper_nightly.txt.zip last_updated.txt /keybase/public/devjacksmith/mh_backups/nightly/'

rm -f *.sql.gz *.txt.zip

echo "===== finished mh_db_auto_backup_nightly.sh ====="

