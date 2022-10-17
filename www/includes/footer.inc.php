<footer>
    <?php
    if (true) :
    // if (UPDATE_AVAILABLE == "yes") : ?>
        <div id="new-release-div">
            <span class="center yellowtext">A new release is available: <?= GIT_VERSION ?></span>
            <a href="?action=update" title="Update to <?= GIT_VERSION ?>">
                <div class="slide-btn-yellow" title="Update now">
                    <img src="resources/icons/update.svg" />
                    <span>Update now</span>
                </div>
            </a>
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