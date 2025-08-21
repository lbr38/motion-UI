# Dockerfile for motion-UI

# Base image
FROM debian:12-slim

# Metadata
LABEL version="1.0" maintainer="lbr38 <motionui@protonmail.com>"

# Variables
ARG WWW_DIR="/var/www/motionui"
ARG DATA_DIR="/var/lib/motionui"
ARG DEBIAN_FRONTEND=noninteractive
ARG fqdn
ARG env
# Debug only
# ARG env=devel

# Install dependencies
RUN apt-get update -y
RUN apt-get install findutils iputils-ping git curl ca-certificates apt-transport-https dnsutils vim gnupg2 ffmpeg mediainfo postfix python3-psutil -y

# Add motion repository
RUN curl -sS https://packages.repomanager.net/repo/gpgkeys/packages.repomanager.net.pub | gpg --dearmor > /etc/apt/trusted.gpg.d/packages.repomanager.net.gpg
# Standby until motion project is releasing a package for version 5.0.0
# TODO: when motion is releasing a new package, use the deb package instead
# RUN echo "deb https://packages.repomanager.net/repo/motion/bookworm/main_prod bookworm main" > /etc/apt/sources.list.d/motion.list

# Add nginx, PHP and go2rtc repositories
RUN echo "deb https://packages.repomanager.net/repo/motionui-nginx/bookworm/nginx_prod bookworm nginx" > /etc/apt/sources.list.d/nginx.list
RUN echo "deb https://packages.repomanager.net/repo/motionui-php/bookworm/main_prod bookworm main" > /etc/apt/sources.list.d/php.list
RUN echo "deb https://packages.repomanager.net/repo/go2rtc/all/main_prod all main" > /etc/apt/sources.list.d/go2rtc.list
RUN apt-get update -y

# Install nginx and PHP 8.3
RUN apt-get install nginx php8.3-fpm php8.3-cli php8.3-sqlite3 php8.3-curl php8.3-yaml php8.3-xml sqlite3 -y

# Install go2rtc
RUN apt-get install go2rtc -y

# Install motion (standby until motion is releasing a package for version 5.0.0)
# RUN apt-get install motion -y
# Build motion from sources
# TODO: when motion is releasing a new package, use the deb package instead
RUN apt-get install -y autoconf automake autopoint build-essential pkgconf libtool libzip-dev libjpeg-dev git libavformat-dev libavcodec-dev libavutil-dev libswscale-dev libavdevice-dev libwebp-dev gettext libmicrohttpd-dev libcamera-tools libcamera-dev libcamera-v4l2
RUN git clone https://github.com/Motion-Project/motion.git && cd motion && autoreconf -fiv && ./configure --prefix=/usr --sysconfdir=/etc && make && make install
RUN rm -rf motion

# Clone project in the container
RUN git clone https://github.com/lbr38/motion-ui.git /tmp/motionui
# For tests only
RUN if [ "$env" = "devel" ]; then \
       cd /tmp/motionui && git checkout devel; \
    fi

# SERVICES CONFIG

# Configure Nginx
RUN mkdir -p /etc/nginx/sites-enabled
RUN cp /tmp/motionui/docker/config/nginx/nginx.conf /etc/nginx/nginx.conf
RUN cp /tmp/motionui/docker/config/nginx/motionui.conf /etc/nginx/sites-enabled/motionui.conf
RUN rm -rf /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf /var/www/html

# Configure PHP
RUN cp /tmp/motionui/docker/config/php/www.conf /etc/php/8.3/fpm/pool.d/www.conf

# Configure SQLite
RUN echo ".headers on" > /root/.sqliterc
RUN echo ".mode column" >> /root/.sqliterc

# Configure Postfix
RUN \cp /tmp/motionui/docker/config/postfix/main.cf /etc/postfix/main.cf
# Copy master.cf with custom listening port 2525 (to avoid conflict with other mail services on the host)
RUN \cp /tmp/motionui/docker/config/postfix/master.cf /etc/postfix/master.cf
RUN sed -i "s/__FQDN__/$fqdn/g" /etc/postfix/main.cf
RUN echo $fqdn > /etc/mailname

# Copy motionui files
RUN mkdir -p $WWW_DIR $DATA_DIR
RUN cp -r /tmp/motionui/www/* $WWW_DIR/
RUN touch $WWW_DIR/.docker
RUN echo "$fqdn" > $WWW_DIR/.fqdn

# Create motionui group and set basic permissions
RUN groupadd motionui
RUN usermod -a -G motionui www-data
RUN chown -R www-data:motionui $WWW_DIR $DATA_DIR

# Configure motion
RUN mkdir -p /var/log/motion /var/run/motion
RUN chown -R www-data:motionui /var/log/motion /var/run/motion
RUN chmod 775 /var/log/motion /var/run/motion

# New main directory is /usr/var/lib/motion
RUN rm /usr/var/lib/motion/* -fr
RUN mkdir -p /usr/var/lib/motion
RUN chown -R www-data:motionui /usr/var/lib/motion
RUN chmod 770 /usr/var/lib/motion
# Create a symbolic link to /etc/motion to keep compatibility
RUN ln -s /usr/var/lib/motion /etc/motion

# Copy motion main config file
RUN cp /tmp/motionui/www/templates/motion/motion.conf /etc/motion/motion.conf

# Copy motion event bin files
RUN mkdir -p /usr/lib/motion
RUN cp /tmp/motionui/www/bin/on_event* /usr/lib/motion/
RUN chown -R www-data:motionui /usr/lib/motion

# Copy motion init script
RUN cp /tmp/motionui/docker/config/motion/init /etc/init.d/motion
RUN chmod 755 /etc/init.d/motion

# Some basic configurations
RUN sed -i 's/# alias ll=/alias ll=/g' /root/.bashrc
RUN echo "set ic" > /root/.vimrc
RUN echo "set mouse-=a" >> /root/.vimrc
RUN echo "syntax on" >> /root/.vimrc
RUN echo "set background=dark" >> /root/.vimrc

# Setup go2rtc

# Copy go2rtc init script
RUN cp /tmp/motionui/docker/config/go2rtc/init /etc/init.d/go2rtc
RUN chmod 755 /etc/init.d/go2rtc
RUN mkdir -p /var/run/go2rtc
RUN chown -R www-data:motionui /var/run/go2rtc
RUN chmod 775 /var/run/go2rtc

# Copy entrypoint script
RUN cp /tmp/motionui/docker/init /init
RUN chmod 700 /init

# Clean
RUN rm -rf /tmp/motionui

# Expose port 8080
EXPOSE 8080

# Set working dir
WORKDIR ${DATA_DIR}

ENTRYPOINT ["/init"]