<footer>
    <div>
        <h5>HELP</h5>
        <div>
            <a target="_blank" rel="noopener noreferrer" href="https://github.com/lbr38/motion-UI/wiki">
                <p class="lowopacity">Documentation<img src="assets/icons/external-link.svg" class="icon" /></p>
            </a>
            <br>
            <a href="mailto:motionui@protonmail.com">
                <p class="lowopacity">Contact</p>
            </a>
        </div>
    </div>

    <div>
        <h5>GITHUB</h5>
        <div class="lowopacity">
            <a target="_blank" rel="noopener noreferrer" href="https://github.com/lbr38/motion-UI" id="github"><img src="assets/images/github.png" /></a>
        </div>
    </div>    

    <div class="text-center margin-auto">
        <p class="lowopacity-cst">motion-UI - release version <?= VERSION ?></p>
        <br>
        <p class="lowopacity-cst">motion-UI is a free and open source software, licensed under the <a target="_blank" rel="noopener noreferrer" href="https://www.gnu.org/licenses/gpl-3.0.en.html">GPLv3</a> license.</p>
    </div>
</footer>

<?php
/**
 *  Load scripts if any
 */
if (is_dir(ROOT . '/public/resources/js')) {
    foreach (glob(ROOT . '/public/resources/js/*.js') as $file) {
        echo '<script type="text/javascript" src="resources/js/' . basename($file) . '"></script>';
    }
} ?>