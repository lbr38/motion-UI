<div id="login-container">
    <div>
        <img id="login-logo" src="/assets/icons/motion.svg" class="mediumopacity-cst" />

        <div id="login-form">
            <form action="/login" method="post" autocomplete="off">
                <div class="flex flex-direction-column row-gap-5">
                    <input type="text" name="username" placeholder="<?= __('Username') ?>" required />
                    <input type="password" name="password" placeholder="<?= __('Password') ?>" required />
                    <button class="btn-large-green margin-top-10" type="submit"><?= __('Login') ?></button>
                </div>
            </form>
        </div>

        <div id="login-error">
            <?php
            // Display authentication errors if any
            if (!empty($loginError)) {
                echo '<p class="font-size-14">' . $loginError . '</p>';
            } ?>
        </div>
    </div>
</div>
