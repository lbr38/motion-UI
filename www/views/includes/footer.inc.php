<footer>
    <p>motion-UI - release version <?= VERSION ?></p>
    <br>

    <?php
    if (UPDATE_AVAILABLE == "yes") : ?>
        <div id="new-release-div">
            <span class="center yellowtext">A new release is available: </span>
            <a href="?action=update">
                <div class="slide-btn-yellow" title="Update now">
                    <img src="resources/icons/update.svg" />
                    <span title="Update now to <?= GIT_VERSION ?>">Update now to <?= GIT_VERSION ?></span>
                </div>
            </a>
        </div>
        <?php
    endif; ?>

    <br>    
    <a target="_blank" id="github" rel="noopener noreferrer" href="https://github.com/lbr38/motion-UI"><img src="resources/images/GitHub-Mark-Light-64px.png" /></a>
</footer>

<script src="resources/js/functions.js"></script>
<script src="resources/js/userspace.js"></script>
<script src="resources/js/settings.js"></script>
<script src="resources/js/motion.js"></script>
<script src="resources/js/live.js"></script>