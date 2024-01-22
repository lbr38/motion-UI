<?php

namespace Controllers\Layout\Tab;

class Motion
{
    public static function render()
    {
        \Controllers\Layout\Container\Render::render('buttons/top');
        \Controllers\Layout\Container\Render::render('motionui/service/status');
        \Controllers\Layout\Container\Render::render('header/general-log-messages');
        \Controllers\Layout\Container\Render::render('getting-started');
        \Controllers\Layout\Container\Render::render('motion/buttons/main');
        \Controllers\Layout\Container\Render::render('buttons/bottom');

        /**
         *  Panels
         */
        \Controllers\Layout\Panel\Notification::render();
        \Controllers\Layout\Panel\Userspace::render();
        \Controllers\Layout\Panel\Settings::render();
        \Controllers\Layout\Panel\Motion\Autostart::render();
        \Controllers\Layout\Panel\Motion\Alert::render();
    }
}
