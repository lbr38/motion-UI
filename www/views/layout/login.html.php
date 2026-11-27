<!DOCTYPE html>
<html lang="<?= \Controllers\App\Lang::detectBrowserLanguage() ?>">
    <?php
    include_once(ROOT . '/views/includes/head.inc.php'); ?>

    <head>
        <link rel="stylesheet" type="text/css" href="/resources/styles/login.css">
    </head>

    <body>
        <?= $content ?>
    </body>
</html>
