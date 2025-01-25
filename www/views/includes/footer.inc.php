<footer class="margin-bottom-40">    
    <div>
        <h5>HELP</h5>
        <a target="_blank" rel="noopener noreferrer" href="https://github.com/lbr38/motion-UI/wiki">
            <span class="lowopacity">Documentation <img src="/assets/icons/external-link.svg" class="icon-small" /></span>
        </a>
        
        <br><br>
        
        <a href="mailto:motionui@protonmail.com">
             <span class="lowopacity">Contact</span>
        </a>
    </div>

    <div>
        <h5>GITHUB</h5>
        <span class="lowopacity">
            <a target="_blank" rel="noopener noreferrer" href="https://github.com/lbr38/motion-UI" id="github"><img src="/assets/icons/github.svg" /></a>
        </span>
    </div>

    <div class="text-center margin-auto">
        <p class="lowopacity-cst">motion-UI - release version <?= VERSION ?></p>
        <br>
        <p class="lowopacity-cst">motion-UI is a free and open source software, licensed under the <a target="_blank" rel="noopener noreferrer" href="https://www.gnu.org/licenses/gpl-3.0.en.html">GPLv3</a> license.</p>
        <br><br><br>
    </div>
</footer>

<script src="/resources/js/functions.js?<?= VERSION ?>"></script>
<script src="/resources/js/general.js?<?= VERSION ?>"></script>
<script src="/resources/js/events/settings.js?<?= VERSION ?>"></script>
<script src="/resources/js/events/user/userspace.js?<?= VERSION ?>"></script>
<script src="/resources/js/events/user/permissions.js?<?= VERSION ?>"></script>
<script src="/resources/js/notification.js?<?= VERSION ?>"></script>

<?php
/**
 *  Additional JS files
 */
if (__ACTUAL_URI__[1] == '' or __ACTUAL_URI__[1] == 'live') {
    $jsFiles = ['events/general', 'camera', 'motion', 'webrtc/webrtc'];
}
if (__ACTUAL_URI__[1] == 'motion') {
    $jsFiles = ['events/general', 'camera', 'events/motion/motion', 'motion'];
}
if (__ACTUAL_URI__[1] == 'events') {
    $jsFiles = ['events/general', 'camera', 'events/motion/event', 'motion'];
}
if (__ACTUAL_URI__[1] == 'stats') {
    $jsFiles = ['events/general', 'functions/motion-charts', 'events/motion/charts'];
}

if (!empty($jsFiles)) {
    foreach ($jsFiles as $jsFile) {
        if (is_file(ROOT . '/public/resources/js/' . $jsFile . '.js')) {
            echo '<script src="/resources/js/' . $jsFile . '.js?' . VERSION . '"></script>';
        }
    }
} ?>