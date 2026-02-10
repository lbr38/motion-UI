<?php
$userLoginController = new \Controllers\User\Login();

// Try to login
try {
    if (!empty($_POST['username']) and !empty($_POST['password'])) {
        $userLoginController->login($_POST['username'], $_POST['password']);
    }
} catch (Exception $e) {
    $loginError = $_['output']['login_error'];
}
