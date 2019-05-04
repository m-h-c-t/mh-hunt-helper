#!/bin/bash
echo "===== started mh_db_auto_backup_nightly.sh ======"

source /var/www/mh-hunt-helper/DB/config.sh
cd /keybase/public/devjacksmith/mh_backups/nightly

# Hunt Helper
echo "====== Backing up hunt helper ====="

if [ -f hunthelper_nightly.sql.gz ]; then
    rm hunthelper_nightly.sql.gz
fi

if [ -f hunthelper_nightly.txt.zip ]; then
    rm hunthelper_nightly.txt.zip
fi

mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events --routines mhhunthelper | gzip -9 > hunthelper_nightly.sql.gz
sleep 5s
rm -rf $MH_DUMP/*
mysqldump -u $MH_USER -p$MH_PASS --host=127.0.0.1 --skip-lock-tables --events --routines -T $MH_DUMP --no-create-info --compatible=db2 mhhunthelper
rm -rf $MH_DUMP/*.sql
zip -j -9 hunthelper_nightly.txt.zip $MH_DUMP/*
rm -rf $MH_DUMP/*

echo "===== finished mh_db_auto_backup_nightly.sh ====="

date > last_updated.txt

