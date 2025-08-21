<head>
    <title>Motion-UI</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- To tell mobile browsers to adjust the width of the window to the width of the device's screen, and set the document scale to 100% of its intended size -->
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=yes">

    <!-- CSS for all pages -->
    <link rel="stylesheet" type="text/css" href="/resources/styles/reset.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/normalize.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/common.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/layout.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/alert.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/icon.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/input.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/button.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/label.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/confirmbox.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/modal.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/tooltip.css?<?= VERSION ?>">
    <link rel="stylesheet" type='text/css' href="/resources/styles/select2.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/motionui.css?<?= VERSION ?>">

    <!-- jQuery -->
    <script src="/resources/js/libs/jquery-3.7.1.min.js?<?= VERSION ?>"></script>
    <!-- Select2 https://select2.org/ -->
    <script src="/resources/js/libs/select2.js?<?= VERSION ?>"></script>
    <!-- Morhpdom -->
    <script src="/resources/js/libs/morphdom-umd.min.js?<?= VERSION ?>"></script>
    <!-- ChartJS -->
    <script src="/resources/js/libs/chartjs-4.4.8.umd.js?<?= VERSION ?>"></script>
    <!-- Stream -->
    <script type="module" src="/resources/js/stream/video-rtc.js?<?= VERSION ?>"></script>
    <script type="module" src="/resources/js/stream/video-stream.js?<?= VERSION ?>"></script>
    <!-- SortableJS -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <!-- App config files -->
    <script src="/resources/js/app/container.config.js?<?= VERSION ?>"></script>

    <?php
    /**
     *  Load pre scripts if any
     */
    if (is_dir(ROOT . '/public/resources/js/pre')) {
        foreach (glob(ROOT . '/public/resources/js/pre/*.js') as $file) {
            echo '<script type="text/javascript" src="/resources/js/pre/' . basename($file) . '?' . VERSION . '"></script>';
        }
    } ?>

    <!-- Favicon -->
    <link rel="icon" href="/assets/favicon.ico" />
</head>