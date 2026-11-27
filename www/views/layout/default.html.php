<!DOCTYPE html>
<html lang="<?= \Controllers\App\Lang::detectBrowserLanguage() ?>">
    <?php
    include_once(ROOT . '/views/includes/head.inc.php'); ?>

    <body>
        <article>
            <?php
                \Controllers\Layout\Container\Render::render('buttons/top');
                \Controllers\Layout\Container\Render::render('motionui/service/status'); ?>

            <!-- Header containers, main-container gives them the page side margins -->
            <div class="main-container">
                <?php
                \Controllers\Layout\Container\Render::render('header/general-log-messages');
                \Controllers\Layout\Container\Render::render('header/debug-mode'); ?>
            </div>

            <?php
                \Controllers\Layout\Container\Render::render('buttons/bottom');
            ?>
        </article>

        <article class="min-height-100vh">
            <?= $content ?>
        </article>

        <?php \Controllers\Layout\Container\Render::render('footer'); ?>

    </body>
</html>