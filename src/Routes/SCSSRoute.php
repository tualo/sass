<?php

namespace Tualo\Office\Sass\Routes;

use Tualo\Office\Basic\Route as BasicRoute;

class SCSSRoute extends \Tualo\Office\Basic\RouteWrapper
{
    public static function scope(): string
    {
        return 'scss.file.route';
    }

    public static function register()
    {
        BasicRoute::add('/scss/(?P<file>[\/.\w\d\-\_\.]+)' . '', function ($matches) {
            CMSRoute::deliverFile($matches);
        }, ['get'], true, [], self::scope());
    }
}
