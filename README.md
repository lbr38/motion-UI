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


## Installation & requirements

Please check the documentation to find all requirements and steps to install **motion-UI**: <a href="https://github.com/lbr38/motion-UI/wiki/Documentation#motion-ui-installation">here</a>


**Default login and password**

- Login: **admin**
- Password: **motionui**


## Documentation

Official documentation is available <a href="https://github.com/lbr38/motion-UI/wiki/Documentation">here</a>.

It should help you starting using **motion-UI**.
