<section class="section-main">
    <div class="div-generic-blue margin-15">
        <h6 class="margin-top-0"><?= __('TABLE LOADING ERROR') ?></h6>

        <p class="note"><?= sprintf(__('An error occurred while trying to load %s table:'), '<code>' . $table . '</code>') ?> <?= strtolower($e->getMessage()) ?></p>
        <?php
        if (!empty($e->getTraceAsString())) {
            echo '<pre class="codeblock copy margin-top-10">' . $e->getTraceAsString() . '</pre>';
        } ?>
    </div>
</section>
