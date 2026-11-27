<?php
if (!IS_ADMIN) {
    throw new Exception(__('You are not allowed to access this panel.'));
}
