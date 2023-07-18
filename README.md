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


## Requirements

### Hardware

- CPU and RAM can be very sollicitated when motion service is running depending on the number of cameras and the resolution of the stream.
- Disk space depends on the number of cameras and the number of days you want to keep images and videos.

### Software and configuration

- **docker** (service must be up and running)
- **docker-compose**
- **A fully qualified domain name** (FQDN) and a valid SSL certificate for this FQDN if you want to access the web interface through a secure connection (https)
- A least a **SPF record** configured for your FQDN, to be able to send email alerts from motion-UI

## Installation and usage

**Important: .deb and .rpm packages are not longer maintained. Please use the docker image instead.**

Official documentation is available <a href="https://github.com/lbr38/motion-UI/wiki">here</a>.

It should help you **installing** and starting using motion-UI.
