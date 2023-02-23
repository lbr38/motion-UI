<?php

namespace Controllers;

require_once('Autoloader.php');

use Exception;

class Controller
{
    public static function render()
    {
        /**
         *  Getting target URI
         */
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = explode('/', $uri);
        $targetUri = $uri[1];

        /**
         *  If target URI is login or logout then load minimal necessary
         */
        if ($targetUri == 'login' or $targetUri == 'logout' or $targetUri == 'stream') {
            Autoloader::loadFromLogin();
        } else {
            Autoloader::load();
        }

        /**
         *  If target URI is 'index.php' then redirect to /
         */
        if ($targetUri == 'index.php') {
            header('Location: /');
        }

        /**
         *  Rendering
         */
        if ($targetUri == '') {
            /**
             *  Render 'main' page
             */
            self::renderMain();
        } elseif ($targetUri == 'live') {
            /**
             *  Render 'live' page
             */
            self::renderLive();
        } elseif ($targetUri == 'stream') {
            /**
             *  Render camera stream
             */
            self::renderStream();
        } elseif ($targetUri == 'image') {
            /**
             *  Render camera image
             */
            self::renderImage();
        } elseif ($targetUri == 'media') {
            /**
             *  Render media file
             */
            self::renderMedia();
        } elseif ($targetUri == 'login') {
            /**
             *  Render login page
             */
            self::renderLogin();
        } elseif ($targetUri == 'logout') {
            /**
             *  Logout
             */
            self::logout();
        } else {
            /**
             *  Render page not found
             */
            self::renderNotfound();
        }
    }

    /**
     *  Render 'main' page
     */
    private static function renderMain()
    {
        $mysettings = new \Controllers\Settings();
        $mymotion = new \Controllers\Motion();
        $mycamera = new \Controllers\Camera();

        /**
         *  Get global settings
         */
        $settings = $mysettings->get();

        /**
         *  Get motion alert status (enabled or disabled)
         */
        $alertEnabled = $mymotion->getAlertStatus();

        /**
         *  Get autostart and alert settings
         */
        $alertConfiguration = $mymotion->getAlertConfiguration();
        $motionActive = $mymotion->motionServiceRunning();
        $motionAutostartEnabled = $mymotion->getAutostartStatus();
        $autostartConfiguration = $mymotion->getAutostartConfiguration();
        $autostartDevicePresenceEnabled = $mymotion->getAutostartOnDevicePresenceStatus();
        $autostartKnownDevices = $mymotion->getAutostartDevices();

        /**
         *  Get total cameras and cameras Ids
         */
        $cameraTotal = $mycamera->getTotal();
        if ($cameraTotal > 0) {
            $cameraIds = $mycamera->getCamerasIds();
        }

        ob_start();
        include_once(ROOT . '/views/main.template.php');
        $content = ob_get_clean();

        include_once(ROOT . '/views/layout.html.php');
    }

    /**
     *  Render 'live' page
     */
    private static function renderLive()
    {
        $mymotion = new \Controllers\Motion();
        $mycamera = new \Controllers\Camera();

        /**
         *  Get all cameras Id
         */
        $cameraTotal = $mycamera->getTotal();

        ob_start();
        include_once(ROOT . '/views/live.template.php');
        $content = ob_get_clean();

        include_once(ROOT . '/views/layout.html.php');
    }

    /**
     *  Render stream
     */
    private static function renderStream()
    {
        include_once(ROOT . '/views/stream.php');
    }

    /**
     *  Render stream
     */
    private static function renderImage()
    {
        include_once(ROOT . '/views/image.php');
    }

    /**
     *  Render media file
     */
    private static function renderMedia()
    {
        include_once(ROOT . '/views/media.php');
    }

    /**
     *  Render login page
     */
    private static function renderLogin()
    {
        include_once(ROOT . '/views/login-layout.html.php');
    }

    /**
     *  Logout
     */
    private static function logout()
    {
        /**
         *  Destruction de la session en cours et redirection vers la page de login
         */

        /**
         *  On démarre la session
         */
        session_start();

        // Réinitialisation du tableau de session
        // On le vide intégralement
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }

        // Destruction de la session
        session_destroy();

        // Destruction du tableau de session
        unset($_SESSION);

        /**
         *  On redirige vers login
         */
        header('Location: /login');

        exit();
    }

    /**
     *  Render page not found using custom error pages
     */
    private static function renderNotfound()
    {
        include_once(ROOT . '/public/custom_errors/custom_404.html');
    }
}
