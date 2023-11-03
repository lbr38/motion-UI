<head>
    <title>Motion-UI</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Pour indiquer aux navigateurs mobiles qu'ils doivent ajuster la largeur de la fenêtre à la largeur de l'écran de l'appareil, et mettre l'échelle du document à 100% de sa taille prévue -->
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="/resources/styles/reset.css"/>
    <link rel="stylesheet" href="/resources/styles/normalize.css"/>
    <link rel="stylesheet" href="/resources/styles/common.css"/>
    <link rel="stylesheet" href="/resources/styles/motionui.css"/>

    <!-- jQuery -->
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
    <!-- ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js" integrity="sha512-QSkVNOCYLtj73J4hbmVoOV6KVZuMluZlioC+trLpewV8qMjsWqlIQvkn1KGX2StWvPMdWGBqim1xlC8krl1EKQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <?php
    /**
     *  Load pre scripts if any
     */
    if (is_dir(ROOT . '/public/resources/js/pre')) {
        foreach (glob(ROOT . '/public/resources/js/pre/*.js') as $file) {
            echo '<script type="text/javascript" src="/resources/js/pre/' . basename($file) . '"></script>';
        }
    } ?>

    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <!-- Favicon -->
    <link rel="icon" href="/assets/favicon.ico" />

    
</head>
