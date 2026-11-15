<?php ob_start(); ?>

<?php
if (NOTIFICATION == 0) : ?>
    <div class="empty-state">
        <p class="empty-state-title"><?= __('Nothing for now!') ?></p>
        <p class="note"><?= __('You have no notifications at the moment. Motion-UI will notify you when an update is available or if there are any important messages to read.') ?></p>
    </div>
    <?php
else :
    // If an update is available, generate the update notification
    if (IS_ADMIN && UPDATE_AVAILABLE) : ?>
        <div class="form-block">
            <h5 class="margin-top-10 margin-bottom-0"><?= __('UPDATE AVAILABLE:') . ' ' . GIT_VERSION ?></h5>

            <div class="flex align-item-center column-gap-10 margin-bottom-10">
                <?php
                // Case its a major release
                if ($currentVersionDigit != $newVersionDigit) : ?>
                    <div class="flex align-item-center column-gap-5">
                        <p class="yellowtext"><b><?= __('Major release') ?></b></p>
                        <img src="/assets/icons/warning.svg" class="icon-medium icon-np" />
                    </div>
                    <span class="dot mediumopacity-cst">●</span>
                    <?php
                endif ?>

                <div class="flex align-item-center column-gap-5">
                    <a href="<?= PROJECT_GIT_REPO ?>/releases/latest" target="_blank" rel="noopener noreferrer" title="<?= __('See changelog') ?>"><p class="mediumopacity"><?= __('Changelog') ?></p></a>
                    <img src="/assets/icons/external-link.svg" class="mediumopacity-cst icon-small icon-np" />
                </div>

                <span class="dot mediumopacity-cst">●</span>
                
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

    // All other notifications
    foreach (NOTIFICATION_MESSAGES as $notification) : ?>
        <div class="form-block">
            <?php
            if (!empty($notification['Title'])) : ?>
                <h5 class="margin-top-10 margin-bottom-10"><?= strtoupper($notification['Title']) ?></h5>
                <?php
            endif ?>

            <div class="flex flex-direction-column">
                <p><?= htmlspecialchars_decode($notification['Message']) ?></p>
            
                <?php
                if (!empty($notification['Id'])) : ?>
                    <button type="button" class="btn-fit-tr acquit-notification-btn margin-top-10" notification-id="<?= $notification['Id'] ?>" title="<?= __('Mark as read') ?>"><?= __('Mark as read') ?></button>
                    <?php
                endif ?>
            </div>
        </div>
        <?php
    endforeach;
endif;

$content = ob_get_clean();
$slidePanelName = 'general/notification';
$slidePanelTitle = __('NOTIFICATIONS');

include(ROOT . '/views/includes/slide-panel.inc.php');
