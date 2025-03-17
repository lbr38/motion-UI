# motion-UI

A dockerized web responsive interface to manage <a href="https://github.com/Motion-Project/motion"><b>motion</b></a> (an open-source motion detection software) and visualize cameras live stream.

<div align="center">
    <img src="https://github.com/user-attachments/assets/964f1307-c295-49f6-82e5-db6326f909b4" width=25% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/caa8944d-5d8e-4b4b-a706-5a2e469dac7e" width=25% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/7a9f1efd-bef0-42b9-ba8f-6c7bde8be028" width=25% align="top">
</div>
<br>
<div align="center">
    <img src="https://github.com/user-attachments/assets/0c3876f5-dfa6-45bd-a750-ca8b4cfe1133" width=25% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/68cd77fd-69cf-46f4-aca2-528d5d42077f" width=25% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/ba3352ea-4174-4707-9f4c-9f6bf2074968" width=25% align="top">
</div>

<br>

## 🚀 Features

- Visualize cameras stream (sound supported).
- Record motion detection (sound supported).
- Enable **autostart and stop** of the motion detection, based on time period or on device presence on the local network. If none of the known devices are connected to the local network then motion detection will be automatically started as nobody is at home.
- Receive email alerts on motion detection.
- Visualize recorded images and videos and download them.
- Record timelapses.

## 📷 Supported cameras

- **USB cameras**
- **Network cameras** (RTSP, HTTP, etc.)

## 📦 Requirements

### Hardware

- CPU and RAM can be very sollicitated when motion detection is running depending on the number of cameras and the resolution of the stream.
- Disk space depends on the number of cameras and the number of days you want to keep images and videos.

### Software and configuration

- **docker** (service must be up and running)
- **A fully qualified domain name** (FQDN) and a valid SSL certificate for this FQDN if you want to access the web interface through a secure connection (https)
- A least a **SPF record** configured for your FQDN, to be able to send email alerts from motion-UI

## 🪛 Installation and usage

Official documentation is available <a href="https://github.com/lbr38/motion-UI/wiki">here</a>.

It should help you **installing** and starting using motion-UI.

## 📱 Android app

An Android app is available for download <a href="https://github.com/lbr38/motion-UI/releases/tag/android-1.0.0">here</a> (in the assets section).

## 📧 Contact

- For bug reports, issues or features requests, please open a new issue in the Github ``Issues`` section
- A Discord channel is available <a href="https://discord.gg/Dn8FurvWfX">here</a> for any questions or quick help/debugging (English or French spoken)
