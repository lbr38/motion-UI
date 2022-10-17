<div id="userspace-div" class="param-slide-container">
    <div class="param-slide">
        <img id="hide-userspace-btn" src="resources/icons/error-close.svg" class="close-btn lowopacity" title="Close" />
        
        <h2 class="center">Userspace</h2>

        <h2>Change password</h2>
       
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

        <a href="logout.php">
            <div class="round-btn-red" title="Logout">
                <span>Logout</span>
            </div>
        </a>
    </div>
</div>