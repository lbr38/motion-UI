<?php

namespace Controllers\Layout\Panel\Motion;

class Alert
{
    public static function render()
    {
        $mymotionAlert = new \Controllers\Motion\Alert();

        $alertConfiguration = $mymotionAlert->getConfiguration();

        include_once(ROOT . '/views/includes/panels/motion/alert.inc.php');
    }
}
