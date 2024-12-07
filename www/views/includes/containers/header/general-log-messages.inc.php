<section class="main-container reloadable-container" container="header/general-log-messages">
    <?php
    /**
     *  Print info or error logs if any
     */
    if (LOG > 0) : ?>
        <div>
            <div class="div-generic-blue flex flex-direction-column row-gap-5">
                <p class="lowopacity-cst">Log messages (<?= LOG ?>)</p>
                <?php
                foreach (LOG_MESSAGES as $log) : ?>
                    <div class="flex justify-space-between">
                        <div class="flex align-item-center column-gap-5">
                            <?php
                            if ($log['Type'] == 'error') {
                                echo '<img src="/assets/icons/error.svg" class="icon">';
                            }
                            if ($log['Type'] == 'info') {
                                echo '<img src="/assets/icons/warning.svg" class="icon">';
                            } ?>
                            <span><?= $log['Date'] . ' ' . $log['Time'] ?> - <?= $log['Component'] ?> - <?= $log['Message'] ?></span>
                        </div>
                        <div class="slide-btn align-self-center acquit-log-btn" log-id="<?= $log['Id'] ?>" title="Mark as read">
                            <img src="/assets/icons/enabled.svg" />
                            <span>Mark as read</span>
                        </div>
                    </div>
                    <?php
                endforeach ?>
            </div>
        </div>
        <?php
    endif ?>
</section>