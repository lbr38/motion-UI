<?php ob_start(); ?>

<h5>PERSONAL INFORMATIONS</h5>

<div>
    <form id="user-edit-info" autocomplete="off">
        <input type="hidden" name="username" value="<?= $_SESSION['username'] ?>" />
        <h6>FIRST NAME</h6>
        <input type="text" class="input-large" name="first-name" value="<?php echo !empty($_SESSION['first_name']) ? $_SESSION['first_name'] : ''; ?>" <?php echo $_SESSION['type'] != 'local' ? 'readonly' : ''; ?>>

        <h6>LAST NAME</h6>
        <input type="text" class="input-large" name="last-name" value="<?php echo !empty($_SESSION['last_name']) ? $_SESSION['last_name'] : ''; ?>" <?php echo $_SESSION['type'] != 'local' ? 'readonly' : ''; ?>>

        <h6>EMAIL</h6>
        <input type="email" class="input-large" name="email" value="<?php echo !empty($_SESSION['email']) ? $_SESSION['email'] : ''; ?>" <?php echo $_SESSION['type'] != 'local' ? 'readonly' : ''; ?>>

        <br><br>
        <button class="btn-small-green">Save</button>
    </form>
</div>

<h5>CHANGE PASSWORD</h5>

<form id="new-password-form" user-id="<?= $_SESSION['id'] ?>">
    <h6 class="required">CURRENT PASSWORD</h6>
    <input type="password" class="input-large" name="actual-password" autocomplete required />

    <h6 class="required">NEW PASSWORD</h6>
    <input type="password" class="input-large" name="new-password" autocomplete required />

    <h6 class="required">NEW PASSWORD (confirm)</h6>
    <input type="password" class="input-large" name="new-password-retype" autocomplete required />
    
    <br><br>
    <button type="submit" class="btn-small-green">Save</button>
</form>

<h5 class="margin-bottom-5">LOGOUT</h5>
<a href="/logout?user">
    <button type="button" class="btn-small-red" title="Logout">Logout</button>
</a>

<?php
if (IS_ADMIN) : ?>
    <h5>USERS</h5>

    <div id="users-settings-container">
        <h6 class="margin-top-0">CREATE USER</h6>
        <p class="note">Create a new user with a specific role.</p>
        <p class="note">Once created, click on the<img src="/assets/icons/update.svg" class="icon-np margin-left-5 margin-right-5" />icon to generate a password.</p>

        <form id="new-user-form" autocomplete="off">
            <div class="flex align-item-center column-gap-10">
                <input type="text" name="username" placeholder="Username" />

                <select name="role" required>
                    <option value="">Select role...</option>
                    <option value="usage">Usage (viewer)</option>
                    <option value="administrator">Administrator</option>
                </select>

                <div>
                    <button class="btn-xxsmall-green" type="submit">+</button>
                </div>
            </div>
        </form>

        <div id="user-settings-generated-passwd"></div>

        <?php
        if (!empty($users)) : ?>
            <div id="currentUsers">
                <h6 class="margin-bottom-5">CURRENT USERS</h6>

                <?php
                foreach ($users as $user) : ?>
                    <div class="table-container grid-fr-2-1 column-gap-15 bck-blue-alt">
                        <div class="flex align-item-center column-gap-10 justify-space-between">
                            <div>
                                <p class="wordbreakall"><?= $user['Username'] ?></p>
                                <p class="mediumopacity-cst"><?= $user['Role_name'] ?></p>
                            </div>

                            <?php
                            if ($user['Role_name'] == 'usage') {
                                echo '<button type="button" class="user-permissions-edit-btn btn-medium-tr" user-id="' . $user['Id'] . '">Edit permissions</button>';
                            } else {
                                echo '<div></div>';
                            } ?>
                        </div>

                        <?php
                        /**
                         *  Print reset and delete buttons if:
                         *  - The current user is a superadmin and the user is not 'admin' (himself)
                         *  - The current user is an admin and the user is not an administrator (so 'usage' users only)
                         */
                        $printButtons = false;

                        if (IS_SUPERADMIN and $user['Username'] != 'admin') {
                            $printButtons = true;
                        }
                        if (IS_ADMIN and $user['Role_name'] == 'usage') {
                            $printButtons = true;
                        }

                        if ($printButtons) : ?>
                            <div class="flex column-gap-10 justify-end">
                                <p class="reset-password-btn" user-id="<?= $user['Id'] ?>" username="<?= $user['Username'] ?>" title="Reset password of user <?= $user['Username'] ?>">
                                    <img src="/assets/icons/update.svg" class="icon-lowopacity" />
                                </p>

                                <p class="delete-user-btn" user-id="<?= $user['Id'] ?>" username="<?= $user['Username'] ?>" title="Delete user <?= $user['Username'] ?>">
                                    <img src="/assets/icons/delete.svg" class="icon-lowopacity" />
                                </p>
                            </div>
                            <?php
                        endif ?>
                    </div>
                    <?php
                endforeach ?>
            </div>
            <?php
        endif ?>
    </div>
    <?php
endif ?>

<br><br>

<?php
$content = ob_get_clean();
$slidePanelName = 'general/user/userspace';
$slidePanelTitle = 'USERSPACE';

include(ROOT . '/views/includes/slide-panel.inc.php');