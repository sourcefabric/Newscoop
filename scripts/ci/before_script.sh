#!/usr/bin/env sh

# create db
mysql -e 'create database newscoop;';

# create db config
cp ./scripts/ci/database_conf.php ./newscoop/conf/

# copy dependencies folder
cp -r ./dependencies/include/* ./newscoop/include/
