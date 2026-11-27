<section class="section-main">
    <div class="div-generic-blue margin-15">
        <h6 class="margin-top-0"><?= __('CONTAINER LOADING ERROR') ?></h6>

        <p class="note"><?= sprintf(__('An error occurred while trying to load %s container:'), '<code>' . htmlspecialchars($container) . '</code>') ?> <?= htmlspecialchars($e->getMessage()) ?></p>
        <?php
        // The stack trace reveals the application internals (files, paths), it is only displayed to administrators
        if (defined('IS_ADMIN') && IS_ADMIN) {
            echo '<pre class="codeblock copy margin-top-10">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        } ?>
    </div>
</section>
