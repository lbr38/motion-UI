# motion-UI

A web responsive interface to manage <a href="https://motion-project.github.io/"><b>motion</b></a> (an open-source motion detection software) and visualize live stream from http cameras.

<div align="center">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/0ce02da3-c23d-444a-9435-7b7c0ea66b13" width=25% align="top">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/94f914a0-62f4-4ebd-947e-ce7aed78a49b" width=25% align="top">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/7c188133-d267-46a3-b12c-8c7401d12c15" width=25% align="top">
</div>
<br>
<div align="center">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/b01953b0-5c60-4ede-ab25-e09d0f575d39" width=25% align="top">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/d238e11d-ac03-4bc0-9112-a7d1c10c960d" width=25% align="top">
    <img src="https://github.com/lbr38/motion-UI/assets/54670129/98f945c9-d03e-4ab3-8041-ea5ac50d3fdb" width=25% align="top">
    
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
- **A fully qualified domain name** (FQDN) and a valid SSL certificate for this FQDN if you want to access the web interface through a secure connection (https)
- A least a **SPF record** configured for your FQDN, to be able to send email alerts from motion-UI

## Installation and usage

**Important: .deb and .rpm packages are not longer maintained. Please use the docker image instead.**

Official documentation is available <a href="https://github.com/lbr38/motion-UI/wiki">here</a>.

It should help you **installing** and starting using motion-UI.
