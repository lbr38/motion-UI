# motion-UI

A web responsive interface to manage <a href="https://motion-project.github.io/"><b>motion</b></a> (open-source motion detection software) and visualize live stream from http cameras.

<div align="center">
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-1.png" width=24% align="top"> 
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-3.png" width=24% align="top">
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-2.png" width=24% align="top">
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-4.png" width=24% align="top">
</div>

<br>

<b>Features</b>

- <b>Start and stop</b> motion service directly from the web interface,
- Enable <b>autostart and stop</b> of the motion service, based on time slots or on device presence on the local network. If none of the known devices are connected to the local network then motion service will be automatically started as nobody is at home.
- Receive <b>mail alerts</b> on motion detection.
- Visualize captured images and videos and/or download them.
- Modify <b>motion</b> configuration files.
- Vizualize http cameras stream.

<hr>

<b>Where to install motion-UI?</b>

You must install <b>motion-UI</b> on a local server that run the <b>motion</b> service.
If you want to access and watch your http cameras stream from <b>motion-UI</b>, the server must have access to those cameras as well.

E.g of a home installation:
<p align="center">
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-draw-io.png">
</p>

As the server is exposed to the Internet, be sure to set up some security between each points with firewall rules / IP filtering.
E.g:
- Camera's http stream must only be accessible by the server on the local network.
- Server's motion-UI must only be accessible from outside by using a VPN.

<hr>

<b>Requirements</b>

- The server running <b>motion</b> and <b>motion-UI</b> must also run a webserver with PHP enabled (apache/nginx + PHP).
- You may need DNS configuration to make sure you can access <b>motion-UI</b> from outside.

<b>Dependencies</b>

Following dependencies are required:

- motion: the motion detection software (if not aleready installed)
- sqlite3: motion-UI systemd service needs to access and insert data in motion-UI database
- mutt: to receive mail alerts when a new motion has been detected
- wget and curl: to check for new release available and download it

```
apt install motion sqlite3 mutt wget curl
```

```
yum install motion sqlite3 mutt wget curl
```

<b>Installation</b>

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

<b>Done</b> ! Access <b>Motion-UI</b> from a web browser. It will automaticaly create necessary files and database.

For this you must create a basic vhost in your web server to serve <b>/var/www/motionui/public</b> directory (with PHP enabled to execute scripts from that directory).

E.g vhost for nginx:

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
    root /var/www/motionui/public;

    # Motion-UI does not have any login page for the moment. You can use a .htpasswd file to set up basic authentication.
    # Uncomment the lines below and generate a .htpasswd file:
    # auth_basic "You must login";
    # auth_basic_user_file /var/www/.htpasswd;

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
        rewrite ^ /index.php;
    }

    location ~ \.php$ {
        root /var/www/motionui/public;
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

<b>Useful</b>

You can use <b>motionui</b> script to perform actions on your installation. Script is located at:

```
/var/lib/motionui/motionui
```

Available parameters are:

<pre>
-i | --install          ➤  Execute Motion-UI installation wizard.
-p | --set-permissions  ➤  Set necessary permissions on Motion-UI directories and files.
-s | --deploy-service   ➤  Deploy Motion-UI service.
</pre>