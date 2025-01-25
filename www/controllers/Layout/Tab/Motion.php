<?php

namespace Controllers\Layout\Tab;

class Motion
{
    public static function render()
    {
        \Controllers\Layout\Container\Render::render('buttons/top');
        \Controllers\Layout\Container\Render::render('motionui/service/status');
        \Controllers\Layout\Container\Render::render('header/general-log-messages');
        \Controllers\Layout\Container\Render::render('motion/buttons/main');
        \Controllers\Layout\Container\Render::render('buttons/bottom');
    }
}
