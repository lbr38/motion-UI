<?php ob_start(); ?> 

<h4>Change password</h4>

<form id="new-password-form" autocomplete="off" username="<?= $_SESSION['username'] ?>">
    <p>Current password:</p>
    <input type="password" class="input-large" name="actual-password" required />
    <br><br>
    <p>New password:</p>
    <input type="password" class="input-large" name="new-password" required />
    <br><br>
    <p>New password (re-type):</p>
    <input type="password" class="input-large" name="new-password-retype" required />
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