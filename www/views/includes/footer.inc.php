<footer class="margin-bottom-30">
    <div class="flex flex-direction-column row-gap-10">
        <div class="flex align-item-center column-gap-5 max-width-fit mediumopacity">
            <img src="/assets/icons/file.svg" class="icon-np" />
            <a target="_blank" rel="noopener noreferrer" href="https://github.com/lbr38/motion-UI/wiki">
                <p>Documentation</p>
            </a>
        </div>

        <div class="flex align-item-center column-gap-5 max-width-fit mediumopacity">
            <img src="/assets/icons/chatbubble.svg" class="icon-np" />
            <a target="_blank" rel="noopener noreferrer" href="https://discord.gg/34yeNsMmkQ">
                <p>Discord</p>
            </a>
        </div>

        <div class="flex align-item-center column-gap-5 max-width-fit mediumopacity">
            <img src="/assets/icons/at-circle.svg" class="icon-np" />
            <a href="mailto:motionui@protonmail.com">
                <p>Contact</p>
            </a>
        </div>

        <div class="flex align-item-center column-gap-5 max-width-fit mediumopacity">
            <img src="/assets/icons/github.svg" class="icon-np" />
            <a target="_blank" rel="noopener noreferrer" href="https://github.com/lbr38/motion-UI">
                <p>GitHub</p>
            </a>
        </div>
    </div> 

    <div class="flex flex-direction-column align-item-center row-gap-10 mediumopacity-cst">
        <img src="/assets/official-logo/logo.svg" class="icon-np" />
        <p class="text-center">Motion-UI - release version <?= VERSION ?></p>
        <p class="text-center">Motion-UI is a free and open source software, licensed under the <a target="_blank" rel="noopener noreferrer" href="https://www.gnu.org/licenses/gpl-3.0.en.html">GPLv3</a> license.</p>
    </div>
</footer>

<!-- Import some classes -->
<script src="/resources/js/classes/Layout.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/Container.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/Table.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/Panel.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/Cookie.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/Alert.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/ConfirmBox.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/Modal.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/Tooltip.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/Select2.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/EChart.js?<?= VERSION ?>"></script>
<script src="/resources/js/classes/System.js?<?= VERSION ?>"></script>

<script>
    const mylayout = new Layout();
    const mycontainer = new Container();
    const mytable = new Table();
    const mypanel = new Panel();
    const mycookie = new Cookie();
    const myalert = new Alert();
    const myconfirmbox = new ConfirmBox();
    const mymodal = new Modal();
    const myselect2 = new Select2();
    const mysystem = new System();
    const mytooltip = new Tooltip();
</script>

<script src="/resources/js/functions.js?<?= VERSION ?>"></script>
<script src="/resources/js/general.js?<?= VERSION ?>"></script>
<script src="/resources/js/events/settings.js?<?= VERSION ?>"></script>
<script src="/resources/js/events/user/userspace.js?<?= VERSION ?>"></script>
<script src="/resources/js/events/user/permissions.js?<?= VERSION ?>"></script>
<script src="/resources/js/events/general/logs.js?<?= VERSION ?>"></script>
<script src="/resources/js/notification.js?<?= VERSION ?>"></script>

<?php
/**
 *  Additional JS classes and files to load, depending on the current page
 */
if (in_array(__ACTUAL_URI__[1], ['', 'live'])) {
    $jsClasses = ['Camera', 'Camera/MSEConnect', 'Camera/WebRTCConnect'];
    $jsFiles = ['events/general', 'events/camera/buttons', 'events/camera/edit', 'events/camera/ptz', 'camera', 'motion', 'stream/webrtc'];
}
if (__ACTUAL_URI__[1] == 'motion') {
    $jsFiles = ['events/general', 'camera', 'events/motion/motion', 'motion'];
}
if (__ACTUAL_URI__[1] == 'events') {
    $jsClasses = ['Motion/Media'];
    $jsFiles = ['events/general', 'camera', 'events/motion/event', 'motion'];
}
if (__ACTUAL_URI__[1] == 'stats') {
    $jsFiles = ['events/general', 'events/motion/stats'];
}

// Load additional JS classes
if (!empty($jsClasses)) {
    foreach ($jsClasses as $class) {
        if (is_file(ROOT . '/public/resources/js/classes/' . $class . '.js')) {
            echo '<script src="/resources/js/classes/' . $class . '.js?' . VERSION . '"></script>';
        }
    }
} ?>

<script>
    <?php
    if (!empty($jsClasses)) {
        foreach ($jsClasses as $class) {
            $name = explode('/', $class);
            $name = end($name);
            echo 'const my' . strtolower(str_replace('/', '', $class)) . ' = new ' . $name . "();\n";
        }
    } ?>
</script>

<?php
// Load additional JS files
if (!empty($jsFiles)) {
    foreach ($jsFiles as $file) {
        if (is_file(ROOT . '/public/resources/js/' . $file . '.js')) {
            echo '<script src="/resources/js/' . $file . '.js?' . VERSION . '"></script>';
        }
    }
} ?>