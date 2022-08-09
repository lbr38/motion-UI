# motion-UI

A web responsive interface to control motion (open-source security camera software) and visualize live stream from http cameras.

<div align="center">
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-1.png" width=24% align="top"> 
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-2.png" width=24% align="top">
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-3.png" width=24% align="top">
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-UI-4.png" width=24% align="top">
</div>

<br>

<b>Where to install motion-UI?</b>

Install <b>motion-UI</b> on a local server that run motion service and has access to your local http camera(s). Access your camera from your PC/smartphone by browsing through <b>motion-UI</b>.

<p align="center">
<img src="https://raw.githubusercontent.com/lbr38/resources/main/screenshots/motionui/motion-draw-io.png">
</p>

As the server is exposed to the Internet, be sure to set up some security between each points with firewall rules / IP filtering.
E.g:
- Camera's http stream must only be accessible by the server on the local network.
- Server's motion-UI must only be accessible from outside by using a VPN.

<hr>

<b>Requirements</b>

- The system running motion must also run a web-service (apache/nginx + PHP).
- No third-party application needed as it is the web server which deliver the control interface. All you need is DNS configuration to make sure you can access the web UI from outside.

<b>Dependencies</b>

You can install the following dependencies or it will be installed automatically by the installation wizard:

```
apt install motion sqlite3 mutt wget curl git
```

```
yum install motion sqlite3 mutt wget curl git
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

Then create a basic vhost in your web server to serve <b>/var/www/motionui/public</b> directory (with PHP enabled to execute scripts from that directory).

Access Motion-UI from a web browser as it will automaticaly create necessary files and database.

Done !