<?php

namespace Tualo\Office\Sass\Routes;

use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Sass\Sass;

class SCSSRoute extends \Tualo\Office\Basic\RouteWrapper
{
    public static function scope(): string
    {
        return 'scss.file.route';
    }

    public static function register()
    {
        BasicRoute::add('/scss/(?P<file>[\/.\w\d\-\_\.]+)' . '', function ($matches) {
            App::logger('SASS')->info("Requesting SCSS file: " . $matches['file']);
            Sass::deliverFile($matches);
        }, ['get'], true, [], 'basic');
    }
}
