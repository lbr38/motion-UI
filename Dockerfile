# Dockerfile for motion-UI

# PHP dependencies (composer), built in a separate stage so composer is not shipped in the final image
FROM composer:2 AS vendor
WORKDIR /app
COPY www/libs/composer.json www/libs/composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --optimize-autoloader

# Motion, built from sources in a separate stage so build tools are not shipped in the final image
# TODO: when motion 5.x.x package is released, use it instead
FROM debian:13-slim AS motion-build
ARG DEBIAN_FRONTEND=noninteractive
RUN apt-get update -y -qq && \
    apt-get install -y -qq ca-certificates git autoconf automake autopoint build-essential pkgconf libtool gettext \
    libzip-dev libjpeg-dev libavformat-dev libavcodec-dev libavutil-dev libswscale-dev libavdevice-dev libwebp-dev libmicrohttpd-dev libcamera-dev && \
    rm -rf /var/lib/apt/lists/*
RUN git clone https://github.com/Motion-Project/motion.git /src/motion && cd /src/motion && \
    git checkout --detach 8cf1700f785fd8938e0fd84f92fe772352e660d9 && \
    autoreconf -fiv && ./configure --prefix=/usr --sysconfdir=/etc && \
    make -j"$(nproc)" && make install DESTDIR=/out
# List the runtime packages providing the shared libraries motion is linked against
RUN for lib in $(ldd /out/usr/bin/motion | awk '/=> \// {print $3}'); do \
        dpkg -S "$lib" 2>/dev/null || dpkg -S "$(realpath "$lib")"; \
    done | cut -d: -f1 | sort -u > /motion-runtime-deps.txt && \
    cat /motion-runtime-deps.txt

# Base image
FROM debian:13-slim

# Metadata
LABEL version="2.0" maintainer="lbr38 <motionui@protonmail.com>"

# Variables
ARG WWW_DIR="/var/www/motionui"
ARG DATA_DIR="/var/lib/motionui"
ARG PHP_VERSION="8.4"
ARG DEBIAN_FRONTEND=noninteractive
ARG branch=main

# Export branch as environment variable for /init script
ENV BRANCH=${branch}

# PACKAGES INSTALL
# Add repositories
ADD https://packages.repomanager.net/repo/gpgkeys/packages.repomanager.net.pub /tmp/packages.repomanager.net.gpg
RUN apt-get update -y -qq && apt-get install -y -qq gnupg2 ca-certificates && rm -rf /var/lib/apt/lists/* && \
    cat /tmp/packages.repomanager.net.gpg | gpg --dearmor > /etc/apt/trusted.gpg.d/packages.repomanager.net.gpg
RUN echo "deb https://packages.repomanager.net/repo/deb/motionui-nginx/trixie/nginx/prod trixie nginx" > /etc/apt/sources.list.d/nginx.list && \
    echo "deb https://packages.repomanager.net/repo/deb/motionui-php/trixie/main/prod trixie main" > /etc/apt/sources.list.d/php.list && \
    echo "deb https://packages.repomanager.net/repo/deb/go2rtc/all/main/prod all main" > /etc/apt/sources.list.d/go2rtc.list
    # TODO: when motion 5.x.x package is released, use it instead
    # echo "deb https://packages.repomanager.net/repo/motion/trixie/main_prod trixie main" > /etc/apt/sources.list.d/motion.list

# Install dependencies
COPY --from=motion-build /motion-runtime-deps.txt /tmp/motion-runtime-deps.txt
RUN apt-get update -y -qq && \
    apt-get install -y -qq findutils iputils-ping apt-transport-https dnsutils vim ffmpeg mediainfo postfix python3-psutil \
    # Install postfix
    postfix \
    # Install motion (standby until motion is releasing a package for version 5.x.x)
    # motion \
    # Install go2rtc
    go2rtc \
    # Install motion runtime dependencies (computed in the motion-build stage)
    # TODO: when motion 5.x.x package is released, use it instead
    $(cat /tmp/motion-runtime-deps.txt) libcamera-tools libcamera-v4l2 \
    # Install nginx and PHP 8.x
    nginx php${PHP_VERSION}-fpm php${PHP_VERSION}-cli php${PHP_VERSION}-sqlite3 php${PHP_VERSION}-curl php${PHP_VERSION}-yaml php${PHP_VERSION}-opcache php${PHP_VERSION}-xml sqlite3 && \
    apt-get -qq autoremove -y && rm -rf /var/lib/apt/lists/* /tmp/motion-runtime-deps.txt

# Copy motion built in the motion-build stage
COPY --from=motion-build /out/ /

# SERVICES CONFIG
# Configure Nginx
RUN rm -rf /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf /var/www/html
COPY docker/config/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/config/nginx/motionui.conf /etc/nginx/sites-enabled/motionui.conf

# Configure PHP
COPY docker/config/php/opcache.ini /etc/php/${PHP_VERSION}/mods-available/opcache.ini
COPY docker/config/php/www.conf /etc/php/${PHP_VERSION}/fpm/pool.d/www.conf

# Configure Postfix
COPY docker/config/postfix/main.cf /etc/postfix/main.cf
# Copy master.cf with custom listening port 2525 (to avoid conflict with other mail services on the host)
COPY docker/config/postfix/master.cf /etc/postfix/master.cf

# Copy motionui files
RUN mkdir -p $WWW_DIR $DATA_DIR
COPY www/ $WWW_DIR/
COPY --from=vendor /app/vendor $WWW_DIR/libs/vendor

# Configure motion
RUN mkdir -p /var/run/motion -m 775 && \
    # New main directory is /usr/var/lib/motion
    rm /usr/var/lib/motion/* -fr && \
    mkdir -p /usr/var/lib/motion -m 770 && \
    # Create a symbolic link to /etc/motion to keep compatibility
    ln -s /usr/var/lib/motion /etc/motion

# Copy motion main config file
COPY www/templates/motion/motion.conf /etc/motion/motion.conf

# Copy motion event bin files
RUN mkdir -p /usr/lib/motion
COPY www/bin/on_event* /usr/lib/motion/
RUN chown -R www-data:www-data /usr/lib/motion

# Copy motion init script
COPY docker/config/motion/init /etc/init.d/motion
RUN chmod 755 /etc/init.d/motion

# Some basic configurations
RUN sed -i 's/# alias ll=/alias ll=/g' /root/.bashrc && \
    echo "set ic" > /root/.vimrc && \
    echo "set mouse-=a" >> /root/.vimrc && \
    echo "syntax on" >> /root/.vimrc && \
    echo "set background=dark" >> /root/.vimrc && \
    # SQLite
    echo ".headers on" > /root/.sqliterc && \
    echo ".mode column" >> /root/.sqliterc

# Setup go2rtc

# Copy go2rtc init script
COPY docker/config/go2rtc/init /etc/init.d/go2rtc
RUN chmod 755 /etc/init.d/go2rtc && \
    mkdir -p /run/go2rtc && \
    chmod 775 /run/go2rtc

# Copy entrypoint script
COPY docker/init /init
RUN chmod 700 /init

# Expose port 8080
EXPOSE 8080

# Set working dir
WORKDIR ${DATA_DIR}

ENTRYPOINT ["/init"]