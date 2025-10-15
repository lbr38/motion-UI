<?php

namespace Controllers\Layout\Tab;

class Live
{
    public static function render()
    {
        \Controllers\Layout\Container\Render::render('cameras/list');
    }
}
