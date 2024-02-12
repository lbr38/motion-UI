# motion-UI

A web responsive interface to manage <a href="https://motion-project.github.io/"><b>motion</b></a> (an open-source motion detection software) and visualize live stream from http cameras.

<div align="center">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/fb0f78f3-10f6-45ef-8e9a-2ce119795493" width=25% align="top">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/3fadc296-4e51-48d1-9454-f956e43f3ec7" width=25% align="top">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/fcd1f4d6-b80d-43e3-8cf0-f09abe9f0e37" width=25% align="top">
</div>
<br>
<div align="center">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/e4194032-8163-4944-bc9d-4783018054cf" width=25% align="top">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/6c1e40d7-950f-4593-9243-5ec4be81e1ea" width=25% align="top">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/28a7d13e-4001-4bd0-822d-2e9b83374cc8" width=25% align="top">
</div>

<br>

## Features

- **Start and stop** motion service directly from the web interface,
- Enable **autostart and stop** of the motion service, based on time slots or on device presence on the local network. If none of the known devices are connected to the local network then motion service will be automatically started as nobody is at home.
- Receive **mail alerts** on motion detection.
- Visualize captured images and videos and/or download them.
- Edit **motion** configuration files.
- Visualize http cameras stream.

## 📱 Android app (new!)

An Android app is available for download <a href="https://github.com/lbr38/motion-UI/releases/tag/android-1.0">here</a> (in the assets section).

<hr>


## Requirements

### Hardware

- CPU and RAM can be very sollicitated when motion service is running depending on the number of cameras and the resolution of the stream.
- Disk space depends on the number of cameras and the number of days you want to keep images and videos.

### Software and configuration

- **docker** (service must be up and running)
- **A fully qualified domain name** (FQDN) and a valid SSL certificate for this FQDN if you want to access the web interface through a secure connection (https)
- A least a **SPF record** configured for your FQDN, to be able to send email alerts from motion-UI

## Installation and usage

Official documentation is available <a href="https://github.com/lbr38/motion-UI/wiki">here</a>.

It should help you **installing** and starting using motion-UI.
