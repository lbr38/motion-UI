# motion-UI

A web responsive interface to manage <a href="https://motion-project.github.io/"><b>motion</b></a> (an open-source motion detection software) and visualize cameras live stream.

<div align="center">
    <img src="https://github.com/user-attachments/assets/870aef98-5e5c-42e0-8387-261f6981561e" width=25% align="top">
    <img src="https://github.com/user-attachments/assets/76b7150b-6439-445d-815f-e899563dacbd" width=25% align="top">
    <img src="https://github.com/user-attachments/assets/26cbd47b-d2c4-483d-8b37-857018876df7" width=25% align="top">
</div>
<br>
<div align="center">
    <img src="https://github.com/user-attachments/assets/93d00121-defb-42ae-b655-83ef339eee0b" width=25% align="top">
    <img src="https://github.com/user-attachments/assets/a8e596cc-e5f5-4123-bae4-d94f3ba7de1d" width=25% align="top">
    <img src="https://github.com/user-attachments/assets/50509fd8-6af0-46e4-be29-0592b43cc306" width=25% align="top">
</div>

<br>

## Features

- Visualize cameras stream.
- **Start and stop** motion service directly from the web interface,
- Enable **autostart and stop** of the motion service, based on time slots or on device presence on the local network. If none of the known devices are connected to the local network then motion service will be automatically started as nobody is at home.
- Receive **mail alerts** on motion detection.
- Visualize captured images and videos and/or download them.
- Create timelapses.

## Android app 📱

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

## Contact

- For bug reports, issues or features requests, please open a new issue in the Github ``Issues`` section
- A Discord channel is available <a href="https://discord.gg/Dn8FurvWfX">here</a> for any questions or quick help/debugging (English or French spoken)
