<!DOCTYPE html>
<html>
    <?php
    if (!defined('ROOT')) {
        define('ROOT', '/var/www/motionui');
    }
    if (!defined('DATA_DIR')) {
        define('DATA_DIR', '/var/lib/motionui');
    }
    require_once(ROOT . '/controllers/Autoloader.php');
    new \Controllers\Autoloader('minimal');
    include_once(ROOT . '/views/includes/head.inc.php');

    $loginErrors = array();
    $error = 0;

    /**
     *  If username and password have been sent
     */
    if (!empty($_POST['username']) and !empty($_POST['password'])) {
        $username = \Controllers\Common::validateData($_POST['username']);
        $myuser = new \Controllers\User\User();

        try {
            /**
             *  Get user Id from username
             */
            $id = $myuser->getIdByUsername($username);

            /**
             *  Checking in database that username/password couple is matching
             */
            $myuser->checkUsernamePwd($id, $_POST['password']);

            /**
             *  Getting all user informations in datbase
             */
            $informations = $myuser->get($id);

            /**
             *  Starting session
             */
            session_start();

            /**
             *  Saving user informations in session variable
             */
            $_SESSION['username']   = $username;
            $_SESSION['id']         = $informations['userId'];
            $_SESSION['role']       = $informations['Role_name'];
            $_SESSION['first_name'] = $informations['First_name'];
            $_SESSION['last_name']  = $informations['Last_name'];
            $_SESSION['email']      = $informations['Email'];
            $_SESSION['type']       = 'local';

            /**
             *  If a '.homepage' file exists then redirect to the specified URI
             */
            if (file_exists(DATA_DIR . '/.homepage')) {
                header('Location: /' . trim(file_get_contents(DATA_DIR . '/.homepage')));
                exit();
            }

            /**
             *  If an 'origin' cookie exists then redirect to the specified URI
             */
            if (!empty($_COOKIE['origin'])) {
                if ($_COOKIE['origin'] != '/logout') {
                    header('Location: ' . $_COOKIE['origin']);
                    exit();
                }
            }

            /**
             *  Else redirect to default page '/'
             */
            header('Location: /');
            exit();
        } catch (Exception $e) {
            $loginErrors[] = $e->getMessage();
        }
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
        <div id="loginDiv-container">

            <div id="loginDiv">
                <img src="/assets/icons/motion.svg" class="margin-bottom-30 mediumopacity-cst" />

                <form action="/login" method="post" autocomplete="off">
                    <input type="text" name="username" placeholder="Username" required />
                    <br>
                    <input type="password" name="password" placeholder="Password" required />
                    <br>
                    <button class="btn-large-green" type="submit">Login</button>
                </form>

                <?php
                /**
                 *  Display authentication errors if any
                 */
                if (!empty($loginErrors)) {
                    foreach ($loginErrors as $loginError) {
                        echo '<p>' . $loginError . '</p>';
                    }
                } ?>
            </div>
        </div>
    </body>
</html>