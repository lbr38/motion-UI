<?php

namespace Controllers\Layout\Tab;

class Live
{
    public static function render()
    {
        \Controllers\Layout\Container\Render::render('motionui/service/status');
        \Controllers\Layout\Container\Render::render('header/general-log-messages');
        \Controllers\Layout\Container\Render::render('getting-started');
        \Controllers\Layout\Container\Render::render('buttons/bottom');
        \Controllers\Layout\Container\Render::render('cameras/list');

        /**
         *  Panels
         */
        \Controllers\Layout\Panel\Notification::render();
        \Controllers\Layout\Panel\Userspace::render();
        \Controllers\Layout\Panel\Camera\Add::render();
        \Controllers\Layout\Panel\Camera\Edit::render();
    }
}
