<div id="userspace-div" class="param-slide-container">
    <div class="param-slide">
        <img id="hide-userspace-btn" src="resources/icons/error-close.svg" class="close-btn lowopacity" title="Close" />
        
        <h2>Userspace</h2>

        <h3>Change password</h3>
       
        <form id="new-password-form" autocomplete="off" username="<?= $_SESSION['username'] ?>">
            <p>Current password:</p>
            <input type="password" class="input-large" name="actual-password" required />
            <br><br>
            <p>New password:</p>
            <input type="password" class="input-large" name="new-password" required />
            <br><br>
            <p>New password (re-type) :</p>
            <input type="password" class="input-large" name="new-password-retype" required />
            <br><br>
            <button class="btn-medium-green">Save</button>
        </form>


        <br><br>
        <a href="logout.php" class="btn-medium-red" title="Logout">Logout</a>
    </div>
</div>