# motion-UI

A web interface to manage your own NVR with <a href="https://github.com/Motion-Project/motion"><b>motion</b></a> (an open-source motion detection software) and visualize cameras live stream.

<div align="center">
    <img src="https://github.com/user-attachments/assets/11c77616-a414-459a-928b-e46e3164ae3f" width=30% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/438d4416-8510-452e-b31b-ac74811b75f0" width=30% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/9532c021-8f99-402d-81b0-dcbb47772e61" width=30% align="top">
</div>
<br>
<div align="center">
    <img src="https://github.com/user-attachments/assets/7effba76-6c5d-4554-bde8-3cf43f112e98" width=30% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/cb84146e-c07f-4e0c-95ae-d5504e269195" width=30% align="top">
    &nbsp;
    <img src="https://github.com/user-attachments/assets/347db0c0-15e0-436f-a85e-3c72c49d00fb" width=30% align="top">
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

An Android app is available for download <a href="https://github.com/lbr38/motion-UI/releases/tag/android-1.0.0">here</a> (in the assets section).

## 📧 Contact

- For bug reports, issues or features requests, please open a new issue in the Github ``Issues`` section
- A Discord channel is available <a href="https://discord.gg/Dn8FurvWfX">here</a> for any questions or quick help/debugging (English or French spoken)
