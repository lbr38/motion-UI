<?php
if (!IS_ADMIN) {
    throw new Exception('You are not allowed to access this panel.');
}
