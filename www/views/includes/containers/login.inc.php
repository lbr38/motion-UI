<div id="login-container">
    <div id="login">
        <img src="/assets/icons/motion.svg" class="margin-bottom-30 mediumopacity-cst" />

        <form id="login-form" action="/login" method="post" autocomplete="off">
            <input type="text" name="username" placeholder="<?= $_['input']['username_placeholder'] ?>" required />
            <br>
            <input type="password" name="password" placeholder="<?= $_['input']['password_placeholder'] ?>" required />
            <br>
            <button class="btn-large-green" type="submit"><?= $_['btn']['submit_text'] ?></button>
        </form>
    </div>

    <?php
    // Display authentication errors if any
    if (!empty($loginError)) : ?>
        <div id="login-error" class="margin-top-10">
            <p><?= $loginError ?></p>
        </div>
        <?php
    endif ?>
</div>
    