<?php

namespace Controllers\Layout\Tab;

class Events
{
    public static function render()
    {
        \Controllers\Layout\Container\Render::render('motion/events/list');
    }
}
