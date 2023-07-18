#!/bin/bash

WWW_DIR="/var/www/motionui"
DATA_DIR="/var/lib/motionui"

/bin/bash $WWW_DIR/bin/motionui -p &

chown -R www-data:motionui $DATA_DIR

# Start services
/usr/sbin/service php8.1-fpm start
/usr/sbin/service nginx start
/usr/sbin/service postfix start

# # Initialize and update database (if needed)
/bin/su -s /bin/bash -c "php $WWW_DIR/tools/initialize-database.php" www-data
/bin/su -s /bin/bash -c "php $WWW_DIR/tools/update-database.php" www-data

# # Start motionui service
php $WWW_DIR/tools/service.php

/bin/bash

tail -f /dev/null