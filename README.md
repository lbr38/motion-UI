# motion-UI

A web responsive interface to control motion (open-source security camera software) and visualize live stream from http cameras.

<div align="center">
<img src="https://raw.githubusercontent.com/lbr38/motion-UI/main/motion-UI-1.jpg" width=30%> <img src="https://raw.githubusercontent.com/lbr38/motion-UI/main/motion-UI-2.jpg" width=30%> <img src="https://raw.githubusercontent.com/lbr38/motion-UI/main/motion-UI-3.jpg" width=30%>
</div>

<b>Where to install motion-UI?</b>

Install <b>motion-UI</b> on a local server that run motion service and has access to your local http camera(s). Access your camera from your PC/smartphone by browsing through <b>motion-UI</b>.

<p align="center">
<img src="https://raw.githubusercontent.com/lbr38/motion-UI/main/motion-draw-io.png">
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
Install mutt to send mail alerts:
<pre>
apt install mutt

yum install mutt
</pre>

<b>System configuration</b>

Add web-user to motion group:
<pre>
usermod -G motion www-data
</pre>

Ensure web-user has sufficient sudo priviledges to start/stop motion service without asking for password (sudo must be installed):
<pre>
# create a new sudoer file :
visudo -f /etc/sudoers.d/www-data

# add this single line :
www-data ALL = (ALL) NOPASSWD: /usr/sbin/service motion start, /usr/sbin/service motion stop
</pre>

<b>Installation</b>

Clone:
<pre>
cd /tmp
git clone https://github.com/lbr38/motion-UI.git
</pre>

Copy <b>www/</b> content to your web root directory. Ensure it has correct permissions:
<pre>
cp -r /tmp/motion-UI/www/* /var/www/motion-UI/
chown -R www-data:www-data /var/www/motion-UI/
</pre>

Then create a basic vhost in your web server to serve <b>/var/www/motion-UI/public</b> directory (with PHP enabled to execute scripts from that directory).

Copy <b>motion/</b> content to <b>/etc/motion/</b>. Ensure it has correct permissions: 
<pre>
cp /tmp/motion-UI/motion/send-alert.sh /etc/motion/

chown -R motion:motion /etc/motion/
chmod 770 /etc/motion/
chmod 660 /etc/motion/*
chmod 700 /etc/motion/send-alert.sh
</pre>

<b>Motion configuration</b>

How to send alerts:

1. Set up a mail client.
- Install mutt
- Create a new configuration file /etc/motion/.muttrc. You can create it anywhere else but it should be readable by motion user.
- Insert your mutt configuration and check if motion can send a mail:
<pre>
sudo -u motion echo '' | mutt -s 'test' -F /etc/motion/.muttrc myemail@mail.com
</pre>

2. Copy send-alert.sh script to /etc/motion/.
- This script will be used to send alerts at the time sheduled in the above configuration.
- Ensure motion user can execute this script:
<pre>
-rwx------   1 motion   motion  2420 juil. 21  2021 send-alert.sh
</pre>

3. Configure motion to send alert on a specific trigger.
The possible triggers are:
- on_event_start
- on_event_end
- on_picture_save
- on_motion_detected
- on_area_detected
- on_movie_start
- on_movie_end
- on_camera_lost

Edit your motion configuration file below and set the desired trigger to execute send-alert.sh.
e.g.
- Send a mail on every new motion detection:
<pre>
on_event_start sh /etc/motion/send-alert.sh -c /etc/motion/.muttrc -r myemail@mail.com -s 'Insert subject here, for example: a new motion has been detected'
</pre>

- Then when motion has generated a video from the last detected motion, it places the video filename inside %f variable which can be used to send a new mail alert with the video attached this time:
<pre>
on_movie_end sh /etc/motion/send-alert.sh -c /etc/motion/.muttrc -r myemail@mail.com -s 'Insert subject here, for example: video of the last detected motion, see attachment' -f %f
</pre>