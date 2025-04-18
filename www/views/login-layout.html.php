<!DOCTYPE html>
<html>
    <?php
    if (!defined('ROOT')) {
        define('ROOT', '/var/www/motionui');
    }

    require_once(ROOT . '/controllers/Autoloader.php');
    new \Controllers\Autoloader('minimal');
    include_once(ROOT . '/views/includes/head.inc.php');

    $userLoginController = new \Controllers\User\Login();

    try {
        /**
         *  Try to login
         */
        if (!empty($_POST['username']) and !empty($_POST['password'])) {
            $userLoginController->login($_POST['username'], $_POST['password']);
        }
    } catch (Exception $e) {
        $loginError = 'Invalid login and/or password';
    } ?>

    <head>
        <meta charset="utf-8">
        <!-- CSS -->
        <link rel="stylesheet" type="text/css" href="/resources/styles/common.css">
        <link rel="stylesheet" type="text/css" href="/resources/styles/motionui.css">

        <!-- Favicon -->
        <link rel="icon" href="/assets/favicon.ico" />
        <title>Login</title>
    </head>

    <body>
        <div id="login-container">
            <div id="login">
                <img src="/assets/icons/motion.svg" class="margin-bottom-30 mediumopacity-cst" />

                <form id="login-form" action="/login" method="post" autocomplete="off">
                    <input type="text" name="username" placeholder="Username" required />
                    <br>
                    <input type="password" name="password" placeholder="Password" required />
                    <br>
                    <button class="btn-large-green" type="submit">Login</button>
                </form>
            </div>

            <?php
            /**
             *  Display authentication errors if any
             */
            if (!empty($loginError)) : ?>
                <div id="login-error" class="margin-top-10">
                    <p><?= $loginError ?></p>
                </div>
                <?php
            endif ?>
        </div>
    </body>
</html>