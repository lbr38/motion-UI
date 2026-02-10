<?php

namespace Controllers\Layout\Tab;

use \Controllers\Layout\Container\Render;

class Login
{
    public static function render()
    {
        Render::render('login');
    }
}
