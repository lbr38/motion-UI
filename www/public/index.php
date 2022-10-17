<?php
define('ROOT', dirname(__FILE__, 2));

require_once(ROOT . '/controllers/Autoloader.php');
\Controllers\Autoloader::load();

/**
 *  Generating template
 */
if (isset($_GET['live'])) {
    $mytemplate = new \Views\Template('live');
} else {
    $mytemplate = new \Views\Template('motion');
}

/**
 *  Rendering template page
 */
$mytemplate->render();

exit();
