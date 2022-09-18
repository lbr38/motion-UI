<?php
if (UPDATE_RUNNING == "yes") :
    if (__ACTUAL_URI__ != "/login.php") : ?>
        <div id="maintenance-container">    
            <div id="maintenance">
                <h3>UPDATE RUNNING</h3>
                <p>motion-UI will be available soon.</p>
                <br>
                <button class="btn-medium-green" onClick="window.location.reload();">Refresh</button>
            </div>
        </div>
        <?php
    endif;
endif;