<?php
ob_start();

if (NOTIFICATION == 0) :
    echo '<p class="note">' . __('Nothing for now!') . '</p>';
else :
    /**
     *  If an update is available, generate the update notification
     */
    if (IS_ADMIN && UPDATE_AVAILABLE) : ?>
        <div class="margin-bottom-50">
            <h5 class="margin-top-10 margin-bottom-0"><?= __('UPDATE AVAILABLE:') . ' ' . GIT_VERSION ?></h5>

            <div class="flex column-gap-10 margin-bottom-10">
                <?php
                // Case its a major release
                if ($currentVersionDigit != $newVersionDigit) : ?>
                    <div class="flex align-item-center column-gap-5">
                        <p class="yellowtext"><b><?= __('Major release') ?></b></p>
                        <img src="/assets/icons/warning.svg" class="icon-medium icon-np" />
                    </div>
                    <?php
                endif ?>

                <div class="flex align-item-center column-gap-5">
                    <a href="<?= PROJECT_GIT_REPO ?>/releases/latest" target="_blank" rel="noopener noreferrer" title="<?= __('See changelog') ?>"><p class="mediumopacity"><?= __('Changelog') ?></p></a>
                    <img src="/assets/icons/external-link.svg" class="mediumopacity-cst icon-small icon-np" />
                </div>
                
                <div class="flex align-item-center column-gap-5">
                    <a href="<?= PROJECT_UPDATE_DOC_URL ?>" target="_blank" rel="noopener noreferrer"><p class="mediumopacity"><?= __('Update instructions') ?></p></a>
                    <img src="/assets/icons/external-link.svg" class="mediumopacity-cst icon-small icon-np" />
                </div>
            </div>

            <div>
                <p class="margin-bottom-5"><?= __('Please follow the upgrade path to update:') ?></p>

                <div class="flex flex-wrap column-gap-5 row-gap-5 align-item-center">
                    <p><a href="<?= PROJECT_GIT_REPO ?>/releases/<?= VERSION ?>" target="_blank" rel="noopener noreferrer" title="<?= __('See changelog') ?>"><code><?= VERSION ?> (<?= __('current') ?>)</code></a></p>

                    <?php
                    if (!empty($upgradePath)) :
                        foreach ($upgradePath as $version) : ?>
                            <img src="/assets/icons/next.svg" class="icon-np" />
                            <p><a href="<?= PROJECT_GIT_REPO ?>/releases/<?= $version ?>" target="_blank" rel="noopener noreferrer" title="<?= __('See changelog') ?>"><code><?= $version ?></code></a></p>
                            <?php
                        endforeach;
                    endif ?>

                    <img src="/assets/icons/next.svg" class="icon-np" />
                    <p><a href="<?= PROJECT_GIT_REPO ?>/releases/<?= GIT_VERSION ?>" target="_blank" rel="noopener noreferrer" title="<?= __('See changelog') ?>"><code class="bkg-green"><?= GIT_VERSION ?> (<?= __('latest') ?>)</code></a></p>
                </div>
            </div>
        </div>
        <?php
    endif;

    foreach (NOTIFICATION_MESSAGES as $notification) :
        if (!empty($notification['Title'])) {
            echo '<h4><b>' . $notification['Title'] . '</b></h4>';
        } ?>
        
        <div class="flex flex-direction-column row-gap-10">
            <p><?= htmlspecialchars_decode($notification['Message']) ?></p>
        
            <?php
            if (!empty($notification['Id'])) : ?>
                <button type="button" class="btn-fit-tr acquit-notification-btn margin-top-10" notification-id="<?= $notification['Id'] ?>" title="<?= __('Mark as read') ?>"><?= __('Mark as read') ?></button>
                <?php
            endif ?>
        </div>
        <br>
        <?php
    endforeach;
endif;

$content = ob_get_clean();
$slidePanelName = 'general/notification';
$slidePanelTitle = __('NOTIFICATIONS');

include(ROOT . '/views/includes/slide-panel.inc.php');
