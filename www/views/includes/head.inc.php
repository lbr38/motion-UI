<head>
    <title>Motion-UI</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- To tell mobile browsers to adjust the width of the window to the width of the device's screen, and set the document scale to 100% of its intended size -->
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="/resources/styles/reset.css?<?= VERSION ?>"/>
    <link rel="stylesheet" href="/resources/styles/normalize.css?<?= VERSION ?>"/>
    <link rel="stylesheet" href="/resources/styles/common.css?<?= VERSION ?>"/>
    <link rel="stylesheet" href="/resources/styles/motionui.css?<?= VERSION ?>"/>

    <!-- jQuery -->
    <script src="/resources/js/jquery/jquery-3.7.1.min.js?<?= VERSION ?>"></script>
    <!-- ChartJS -->
    <script src="/resources/js/chartjs/chart-3.7.1.min.js?<?= VERSION ?>"></script>

    <?php
    /**
     *  Load pre scripts if any
     */
    if (is_dir(ROOT . '/public/resources/js/pre')) {
        foreach (glob(ROOT . '/public/resources/js/pre/*.js') as $file) {
            echo '<script type="text/javascript" src="/resources/js/pre/' . basename($file) . '?' . VERSION . '"></script>';
        }
    } ?>

    <!-- <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" /> -->

    <!-- Favicon -->
    <link rel="icon" href="/assets/favicon.ico" />
</head>