<?php ob_start(); ?>

<form id="user-permissions-form" user-id="<?= $userId ?>">

    <h6>GRANT ACCESS TO CAMERAS</h6>
    <select id="user-permissions-cameras-select" name="cameras" user-id="<?= $userId ?>" multiple>
        <?php
        foreach ($cameras as $camera) :
            $selected = false;

            try {
                $configuration = json_decode($camera['Configuration'], true, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new Exception('Failed to decode camera #' . $camera['Id'] . ' configuration: ' . $e->getMessage());
            }

            // Check if camera is already granted
            if (isset($permissions['cameras_access']) and is_array($permissions['cameras_access'])) {
                if (in_array($camera['Id'], $permissions['cameras_access'])) {
                    $selected = true;
                }
            } ?>

            <option value="<?= $camera['Id'] ?>" <?= $selected ? 'selected' : '' ?>><?= strtoupper($configuration['name']) ?></option>
            <?php
        endforeach ?>
    </select>

    <br><br>
    <button type="submit" class="btn-small-green">Save</button>
</form>

<script>
$(document).ready(function(){
    myselect2.convert('#user-permissions-cameras-select', 'Select cameras...');
});
</script>

<?php
$content = ob_get_clean();
$slidePanelName = 'general/user/permissions';
$slidePanelTitle = 'EDIT USER PERMISSIONS';

include(ROOT . '/views/includes/slide-panel.inc.php');
