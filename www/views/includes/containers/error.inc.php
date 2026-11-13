<section class="section-main">
    <h3><?= __('ERROR') ?></h3>

    <div class="div-generic-blue">
        <p><?= sprintf(__('An error occurred while trying to load %s container:'), '<code>' . $container . '</code>') ?> <?= $e->getMessage() ?></p>
    </div>
</section>
