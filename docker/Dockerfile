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

# Install dependencies, postfix
RUN apt-get update -y
RUN apt-get install findutils iputils-ping git curl ca-certificates apt-transport-https dnsutils vim gnupg2 ffmpeg postfix -y

# Add motion 4.5.x repository
RUN curl -sS https://packages.repomanager.net/repo/gpgkeys/packages.repomanager.net.pub | gpg --dearmor > /etc/apt/trusted.gpg.d/packages.repomanager.net.gpg
RUN echo "deb https://packages.repomanager.net/repo/motion/bookworm/main_prod bookworm main" > /etc/apt/sources.list.d/motion.list

# Add nginx and PHP repositories
RUN echo "deb https://packages.repomanager.net/repo/motionui-nginx/bookworm/nginx_prod bookworm nginx" > /etc/apt/sources.list.d/nginx.list
RUN echo "deb https://packages.repomanager.net/repo/motionui-php/bookworm/main_prod bookworm main" > /etc/apt/sources.list.d/php.list
RUN apt-get update -y

# Install nginx and PHP 8.3
RUN apt-get install nginx php8.3-fpm php8.3-cli php8.3-sqlite3 php8.3-curl php8.3-yaml sqlite3 -y

# Install motion
RUN apt-get install motion -y

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
RUN rm -rf /etc/nginx/sites-enabled/default /var/www/html

# Configure PHP
RUN cp /tmp/motionui/docker/config/php/www.conf /etc/php/8.3/fpm/pool.d/www.conf

# Configure SQLite
RUN echo ".headers on" > /root/.sqliterc
RUN echo ".mode column" >> /root/.sqliterc

# Configure Postfix
RUN \cp /tmp/motionui/docker/config/postfix/main.cf /etc/postfix/main.cf
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
RUN usermod -a -G motion www-data
RUN usermod -a -G motionui motion
RUN chown -R www-data:motionui $WWW_DIR $DATA_DIR

# Configure motion
RUN mkdir -p /var/log/motion /var/run/motion
RUN touch /var/run/motion/motion.pid
RUN chown -R motion:motion /var/log/motion /var/run/motion
RUN chmod 775 /var/log/motion /var/run/motion

# Copy motion main config file
RUN rm /etc/motion/* -fr
RUN cp /tmp/motionui/www/templates/motion/motion.conf /etc/motion/motion.conf
RUN chown -R www-data:motion /etc/motion

# Copy motion event bin files
RUN mkdir -p /usr/lib/motion
RUN cp /tmp/motionui/www/bin/on_event* /usr/lib/motion/
RUN chown -R motion:motion /usr/lib/motion

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
# First, check if a binary is available for the current architecture
RUN ARCH=$(dpkg --print-architecture); \
    if [ ! -f "${WWW_DIR}/bin/go2rtc/go2rtc_linux_${ARCH}" ]; then \
        echo "No go2rtc binary available for this architecture (${ARCH})"; \
        exit 1; \
    fi
# Then link go2rtc binary to /usr/local/bin/ and set permissions
RUN ln -s ${WWW_DIR}/bin/go2rtc/go2rtc_linux_$(dpkg --print-architecture) /usr/local/bin/go2rtc
RUN chmod 755 /usr/local/bin/go2rtc
# Copy go2rtc template config file if not exists
RUN if [ ! -f "${DATA_DIR}/go2rtc/go2rtc.yml" ]; then \
        mkdir -p ${DATA_DIR}/go2rtc; \
        cp ${WWW_DIR}/templates/go2rtc/go2rtc.yml ${DATA_DIR}/go2rtc/go2rtc.yml; \
        chown www-data:motionui ${DATA_DIR}/go2rtc/go2rtc.yml; \
    fi

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