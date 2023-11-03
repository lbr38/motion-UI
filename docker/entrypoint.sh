#!/bin/bash

WWW_DIR="/var/www/motionui"
DATA_DIR="/var/lib/motionui"

/bin/bash $WWW_DIR/bin/motionui -p &
chown -R www-data:motionui $DATA_DIR

# Docker run options
# when
# -e FQDN=server.example.com
# are set, the following settings are changed:
if [ ! -z "$FQDN" ];then
    # Postfix/mail configuration
    postconf -e "myhostname = $FQDN"
    echo $FQDN > /etc/mailname

    # motion-UI configuration
    echo $FQDN > "$WWW_DIR/.fqdn"
fi

# Start services
/usr/sbin/service php8.1-fpm start
/usr/sbin/service nginx start
/usr/sbin/service postfix start

# # Initialize and update database (if needed)
/bin/su -s /bin/bash -c "php $WWW_DIR/tools/initialize-database.php" www-data
/bin/su -s /bin/bash -c "php $WWW_DIR/tools/update-database.php" www-data

# # Start motionui service
/bin/su -s /bin/bash -c "php $WWW_DIR/tools/service.php" www-data

/bin/bash