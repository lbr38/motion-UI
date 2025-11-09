<div align="center">
    <img src="https://raw.githubusercontent.com/lbr38/motion-UI/refs/heads/devel/images/readme/github-readme-black.png#gh-light-mode-only" align="top">
</div>

<div align="center">
    <img src="https://raw.githubusercontent.com/lbr38/motion-UI/refs/heads/devel/images/readme/github-readme-white.png#gh-dark-mode-only" align="top" width=70%>
</div>

<br>

A web interface to manage your own NVR with <a href="https://github.com/Motion-Project/motion"><b>motion</b></a> (an open-source motion detection software) and visualize cameras live stream.

<br>

<div align="center">
    <img src="https://raw.githubusercontent.com/lbr38/motion-UI/refs/heads/devel/images/readme/screenshot01.png" width=30% align="top">
    &nbsp;
    <img src="https://raw.githubusercontent.com/lbr38/motion-UI/refs/heads/devel/images/readme/screenshot02.png" width=30% align="top">
    &nbsp;
    <img src="https://raw.githubusercontent.com/lbr38/motion-UI/refs/heads/devel/images/readme/screenshot03.png" width=30% align="top">
</div>
<br>
<div align="center">
    <img src="https://raw.githubusercontent.com/lbr38/motion-UI/refs/heads/devel/images/readme/screenshot04.png" width=30% align="top">
    &nbsp;
    <img src="https://raw.githubusercontent.com/lbr38/motion-UI/refs/heads/devel/images/readme/screenshot05.png" width=30% align="top">
    &nbsp;
    <img src="https://raw.githubusercontent.com/lbr38/motion-UI/refs/heads/devel/images/readme/screenshot06.png" width=30% align="top">
</div>

<br>

## 🚀 Features

- Visualize cameras stream (sound supported).
- Record motion detection (sound supported).
- Motion detection autostart based on time period.
- Motion detection autostart based on device presence on the local network. If none of the known devices are connected to the local network then motion detection will be automatically started as nobody is at home.
- Email alerts on motion detection.
- Visualize recorded images and videos and download them.
- Create timelapses.

## 📷 Supported cameras

- **Network cameras** (RTSP, HTTP, etc.)
- **USB cameras**

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

Get the android app to visualize your cameras and manage your motion-UI instance on your smartphone.

[<img src="https://fdroid.gitlab.io/artwork/badge/get-it-on.png"
    alt="Get it on F-Droid"
    height="80">](https://f-droid.org/packages/app.motionui.android)

## 📧 Contact

- For bug reports, issues or features requests, please open a new issue in the Github ``Issues`` section
- A Discord channel is available <a href="https://discord.gg/Dn8FurvWfX">here</a> for any questions or quick help/debugging (English or French spoken)
