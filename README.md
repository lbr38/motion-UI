# motion-UI

A web responsive interface to manage <a href="https://motion-project.github.io/"><b>motion</b></a> (an open-source motion detection software) and visualize live stream from http cameras.

<div align="center">
    <img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-1.png" width=25% align="top"> 
    <img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-events.png" width=25% align="top">
    <img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-metrics.png" width=25% align="top">
</div>
<br>
<div align="center">
    <img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-autostart.png" width=25% align="top">
    <img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-alerts.png" width=25% align="top">
    <img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-4.png" width=25% align="top">
</div>

<br>

## Features

- **Start and stop** motion service directly from the web interface,
- Enable **autostart and stop** of the motion service, based on time slots or on device presence on the local network. If none of the known devices are connected to the local network then motion service will be automatically started as nobody is at home.
- Receive **mail alerts** on motion detection.
- Visualize captured images and videos and/or download them.
- Edit **motion** configuration files.
- Visualize http cameras stream.

<hr>

## Where to install motion-UI?

You must install **motion-UI** on the same host/server that runs the **motion** service.

If you want to access and watch your http cameras stream from **motion-UI**, the server must have access to those cameras as well.

E.g of a home installation:
<p align="center">
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-draw-io.png">
</p>

As the server is exposed to the Internet, be sure to set up some security between each points with firewall rules / IP filtering.
E.g:
- Camera's http stream must only be accessible by the server on the local network.
- Server's motion-UI must only be accessible from outside by using a VPN.

<hr>

## Requirements

Following **dependencies** are required:

- **motion**: the motion detection software (if not aleready installed)
- **sqlite**: motion-UI service may need to access and insert data in motion-UI database
- **mutt**: to receive mail alerts when a new motion has been detected
- **curl**: to check for new available release

**Installation on a Debian system:**

```
apt install motion sqlite3 mutt curl
```

**Installation on a Redhat/CentOS system:**

```
yum install motion sqlite mutt curl
```

- The server running **motion** must run a webserver with PHP enabled (nginx recommended) to run **motion-UI**.

**Installation on a Debian system** (you will need to have access to a repository providing PHP8.1 packages):

```
apt install nginx php8.1-fpm php8.1-cli php8.1-sqlite3 php8.1-curl
```

**Installation on a Redhat/CentOS system** (you will need to have access to a repository providing PHP8.1 packages):

```
yum install nginx php-fpm php-cli php-pdo php-curl
```

- You later may need a **domain name** and DNS configuration to make sure you can access **motion-UI** from outside.

## Motion-UI installation

Clone:

```
cd /tmp
git clone https://github.com/lbr38/motion-UI.git
```

Execute installation script (with sudo):

```
cd /tmp/motion-UI/
sudo ./motionui --install
```

**Done**! You must complete your webserver and vhost configuration to access **motion-UI** from a web browser.

## Serving motion-UI

You must create a basic vhost in your web server to serve **/var/www/motionui/public** directory (with PHP enabled to execute scripts from that directory).

I can't provide full nginx and PHP configuration but here is an e.g vhost for nginx below. Be sure to adapt some values:

```
server unix:/var/run/php-fpm/php-fpm.sock; => path to PHP unix socket
SERVER-IP                                  => Server IP address
SERVERNAME.MYDOMAIN.COM                    => motion-UI dedicated domain name 
PATH-TO-CERTIFICATE                        => SSL certificate and key files names
set $WWW_DIR '/var/www/motionui';          => specify root path to the motion-UI directory (default is /var/www/motionui)
```

```
# Path to unix socket
upstream php-handler {
    server unix:/var/run/php-fpm/php-fpm.sock;
}

server {
    listen SERVER-IP:80;
    server_name SERVERNAME.MYDOMAIN.COM;

    # Force https
    return 301 https://$server_name$request_uri;

    # Path to log files
    access_log /var/log/nginx/SERVERNAME.MYDOMAIN.COM_access.log;
    error_log /var/log/nginx/SERVERNAME.MYDOMAIN.COM_error.log;
}

server {
    # Set motion-UI web directory location
    set $WWW_DIR '/var/www/motionui'; # default is /var/www/motionui

    listen SERVER-IP:443 ssl;
    server_name SERVERNAME.MYDOMAIN.COM;

    # Path to log files
    access_log /var/log/nginx/SERVERNAME.MYDOMAIN.COM_ssl_access.log combined;
    error_log /var/log/nginx/SERVERNAME.MYDOMAIN.COM_ssl_error.log;

    # Path to SSL certificate/key files
    ssl_certificate PATH-TO-CERTIFICATE.crt;
    ssl_certificate_key PATH-TO-PRIVATE-KEY.key;

    # Add headers to serve security related headers
    add_header Strict-Transport-Security "max-age=15768000; includeSubDomains; preload;" always;
    add_header Referrer-Policy "no-referrer" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Download-Options "noopen" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Permitted-Cross-Domain-Policies "none" always;
    add_header X-Robots-Tag "none" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Remove X-Powered-By, which is an information leak
    fastcgi_hide_header X-Powered-By;

    # Path to motionui root dir
    root $WWW_DIR/public;

    # Enable gzip
    gzip on;
    gzip_vary on;
    gzip_comp_level 4;
    gzip_min_length 256;
    gzip_proxied expired no-cache no-store private no_last_modified no_etag auth;
    gzip_types application/atom+xml application/javascript application/json application/ld+json application/manifest+json application/rss+xml application/vnd.geo+json application/vnd.ms-fontobject application/x-font-ttf application/x-web-app-manifest+json application/xhtml+xml application/xml font/opentype image/bmp image/svg+xml image/x-icon text/cache-manifest text/css text/plain text/vcard text/vnd.rim.location.xloc text/vtt text/x-component text/x-cross-domain-policy;

    location = /robots.txt {
        deny all;
        log_not_found off;
        access_log off;
    }

    location / {
        index index.php;
    }

    location ~ \.php$ {
        root $WWW_DIR/public;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $request_filename;
        #include fastcgi.conf;
        fastcgi_param HTTPS on;
        # Avoid sending the security headers twice
        fastcgi_param modHeadersAvailable true;
        fastcgi_pass php-handler;
        fastcgi_intercept_errors on;
        fastcgi_request_buffering off;
    }

    location ~ \.(?:css|js|svg|gif|map|png|html|ttf|ico|jpg|jpeg)$ {
        try_files $uri $uri/ =404;
        access_log off;
    }
}
```

Once the configuration is applied, you can access motion-UI through a web browser, log in using default login below:

**Default login and password**

- Login: **admin**
- Password: **motionui**

<h2>Documentation</h2>

Official documentation is available <a href="https://github.com/lbr38/motion-UI/wiki/Documentation">here</a>.

It should help you starting using **motion-UI**.

## Useful

You can use the **motionui** script to perform actions on your installation. Script is located at:

```
/var/lib/motionui/motionui
```

Available parameters are:

<pre>
-i | --install          ➤  Execute motion-UI installation wizard.
-p | --set-permissions  ➤  Set necessary permissions on motion-UI directories and files.
-s | --deploy-service   ➤  Deploy motion-UI service.
</pre>