<?php

namespace Controllers\Layout\Tab;

class Main
{
    public static function render()
    {
        \Controllers\Layout\Container\Render::render('motionui/service/status');
        \Controllers\Layout\Container\Render::render('header/general-log-messages');
        \Controllers\Layout\Container\Render::render('getting-started');
        \Controllers\Layout\Container\Render::render('buttons/main');
        \Controllers\Layout\Container\Render::render('buttons/bottom');
        \Controllers\Layout\Container\Render::render('motion/events/list');
        \Controllers\Layout\Container\Render::render('motion/stats/list');
        \Controllers\Layout\Container\Render::render('cameras/list');

        /**
         *  Panels
         */
        \Controllers\Layout\Panel\Settings::render();
        \Controllers\Layout\Panel\Notification::render();
        \Controllers\Layout\Panel\Userspace::render();
        \Controllers\Layout\Panel\Camera\Add::render();
        \Controllers\Layout\Panel\Camera\Edit::render();
        \Controllers\Layout\Panel\Motion\Autostart::render();
        \Controllers\Layout\Panel\Motion\Alert::render();
    }
}
