<footer>
    <?php
    if (UPDATE_AVAILABLE == "yes") : ?>
        <div id="new-release-div">
            <span class="center yellowtext">New release available: <?= GIT_VERSION ?></span>
            <a href="?action=update" title="Update to <?= GIT_VERSION ?>"><button class="btn-medium-yellow">↻ Update now</button></a>
        </div>
        <?php
    endif; ?>

    <br>

    <span class="block center lowopacity">
        <a target="_blank" rel="noopener noreferrer" href="https://github.com/lbr38/motion-UI" class="icon">motion-UI - github <img src="resources/icons/github.png" class="icon" /></a>
    </span>
</footer>

<script src="resources/js/functions.js"></script>
<script src="resources/js/userspace.js"></script>
<script src="resources/js/settings.js"></script>
<script src="resources/js/motion.js"></script>
<script src="resources/js/camera.js"></script>