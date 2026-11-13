<?php
// Get date from cookies if there are, else set today's date
if (empty($_COOKIE['event-date'])) {
    $eventDate = DATE_YMD;
} else {
    $eventDate = $_COOKIE['event-date'];
} ?>

<section class="main-container">
    <div class="flex flex-wrap align-item-center column-gap-20">
        <div>
            <h6><?= __('SELECT DATE') ?></h6>
            <input type="date" class="input-medium event-date-input" value="<?= $eventDate ?>" />
        </div>

        <div>
            <h6><?= __('FILTER BY CAMERA') ?></h6>
            <select id="events-filter-cameras" class="select-large" multiple>
                <?php
                foreach ($cameras as $camera) :
                    if (!empty($_COOKIE['tmp/events-filter-cameras']) and in_array($camera['Id'], explode(',', $_COOKIE['tmp/events-filter-cameras']))) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    } ?>

                    <option value="<?= $camera['Id'] ?>" <?= $selected ?>><?= $camera['Name'] ?></option>
                    <?php
                endforeach ?>
            </select>
        </div>
    </div>
</section>

<script>
    $('#events-filter-cameras').ready(function(){
        myselect2.convert('select#events-filter-cameras', <?= json_encode(__('Select camera(s)...')) ?>, false);
    });
</script>