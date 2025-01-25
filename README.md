# motion-UI

A dockerized web responsive interface to manage <a href="https://github.com/Motion-Project/motion"><b>motion</b></a> (an open-source motion detection software) and visualize cameras live stream.

<div align="center">
    <img src="https://github.com/user-attachments/assets/bdae2550-819d-40c4-895b-541ee64bdc03" width=25% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/afe3e48a-3a26-4e75-a6a7-a97b2ac2bf9e" width=25% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/a2472f8b-24fc-4967-bb6a-f8ad8af95270" width=25% align="top">
</div>
<br>
<div align="center">
    <img src="https://github.com/user-attachments/assets/cb9137c7-484a-4c2c-ad0f-c33ef7a602bd" width=25% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/81c05e3f-599d-4cc1-9d9a-9748fce54763" width=25% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/04b18116-2af0-4bd3-8438-e9f1fed8c7ed" width=25% align="top">
</div>

<br>

## 🚀 Features

- Visualize cameras stream (sound supported).
- Record motion detection (sound supported).
- Enable **autostart and stop** of the motion detection, based on time slots or on device presence on the local network. If none of the known devices are connected to the local network then motion detection will be automatically started as nobody is at home.
- Receive email alerts on motion detection.
- Visualize recorded images and videos and download them.
- Record timelapses.

## 📷 Supported cameras

- **USB cameras**
- **Raspberry Pi cameras**
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
