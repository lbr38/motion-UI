<head>
    <title>Motion-UI</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- To tell mobile browsers to adjust the width of the window to the width of the device's screen, and set the document scale to 100% of its intended size -->
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=yes">

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico">

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
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/scrollbar.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/echart.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/daterangepicker.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/components/hide.css?<?= VERSION ?>">
    <link rel="stylesheet" type='text/css' href="/resources/styles/select2.css?<?= VERSION ?>">
    <link rel="stylesheet" type="text/css" href="/resources/styles/motionui.css?<?= VERSION ?>">

    <!-- Translations for the javascript code -->
    <script>
        const LANG = <?= json_encode(\Controllers\App\Lang::get()) ?>;
        const TRANSLATIONS = <?= json_encode(\Controllers\App\Lang::catalogue('javascript'), JSON_FORCE_OBJECT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
    </script>

    <!-- Load pre JS -->
    <script type="text/javascript" src="/resources/js/pre/functions/lang.js?<?= VERSION ?>"></script>
    <script type="text/javascript" src="/resources/js/pre/functions/global.js?<?= VERSION ?>"></script>
    <!-- jQuery -->
    <script src="/resources/js/libs/jquery-3.7.1.min.js?<?= VERSION ?>"></script>
    <!-- Select2 https://select2.org/ -->
    <script src="/resources/js/libs/select2.js?<?= VERSION ?>"></script>
    <!-- Morhpdom -->
    <script src="/resources/js/libs/morphdom-umd.min.js?<?= VERSION ?>"></script>
    <!-- ECharts -->
    <script src="/resources/js/libs/echarts.min.js?<?= VERSION ?>"></script>
    <!-- Moment & Daterangepicker -->
    <script src="/resources/js/libs/moment.min.js?<?= VERSION ?>"></script>
    <script src="/resources/js/libs/daterangepicker.min.js?<?= VERSION ?>"></script>
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
</head>