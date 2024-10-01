<?php ob_start(); ?> 

<h4>Change password</h4>

<form id="new-password-form" username="<?= $_SESSION['username'] ?>">
    <h6>CURRENT PASSWORD</h6>
    <input type="password" class="input-large" name="actual-password" autocomplete required />

    <h6>NEW PASSWORD</h6>
    <input type="password" class="input-large" name="new-password" autocomplete required />

    <h6>NEW PASSWORD (re-type)</h6>
    <input type="password" class="input-large" name="new-password-retype" autocomplete required />
    
    <br><br>
    <button type="submit" class="btn-small-green">Save</button>
</form>

<br><br>

<a href="/logout">
    <div class="btn-small-red" title="Logout">
        <span>Logout</span>
    </div>
</a>

<?php
$content = ob_get_clean();
$slidePanelName = 'userspace';
$slidePanelTitle = 'USERSPACE';

include(ROOT . '/views/includes/slide-panel.inc.php');