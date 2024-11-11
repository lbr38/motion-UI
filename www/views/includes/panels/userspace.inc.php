<?php ob_start(); ?>

<h5>CHANGE PASSWORD</h5>

<form id="new-password-form" username="<?= $_SESSION['username'] ?>">
    <h6>CURRENT PASSWORD</h6>
    <input type="password" class="input-large" name="actual-password" autocomplete required />

    <h6>NEW PASSWORD</h6>
    <input type="password" class="input-large" name="new-password" autocomplete required />

    <h6>NEW PASSWORD (confirm)</h6>
    <input type="password" class="input-large" name="new-password-retype" autocomplete required />
    
    <br><br>
    <button type="submit" class="btn-small-green">Save</button>
</form>

<h5>LOGOUT FROM MOTION-UI</h5>
<a href="/logout">
    <button type="button" class="btn-small-red" title="Logout">Logout</button>
</a>

<?php
$content = ob_get_clean();
$slidePanelName = 'userspace';
$slidePanelTitle = 'USERSPACE';

include(ROOT . '/views/includes/slide-panel.inc.php');