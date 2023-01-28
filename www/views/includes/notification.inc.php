<div id="notification-div" class="param-slide-container">
    <div class="param-slide">
        <img id="hide-notification-btn" src="resources/icons/error-close.svg" class="close-btn lowopacity" title="Close" />
        
        <h2 class="center">Notifications</h2>

        <?php
        if (NOTIFICATION == 0) {
            echo 'Nothing for now!';
        } else {
            foreach (NOTIFICATION_MESSAGES as $notification) {
                if (!empty($notification['title'])) {
                    echo '<h4><b>' . $notification['title'] . '</b></h4>';
                }
                echo '<p>' . $notification['message'] . '</p><br><br>';
            }
        } ?>
    </div>
</div>