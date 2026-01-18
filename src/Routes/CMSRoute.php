<?php

namespace Tualo\Office\Sass\Routes;

use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Sass\Sass;

class CMSRoute extends \Tualo\Office\Basic\RouteWrapper
{
    public static function scope(): string
    {
        return 'scss.cms.route';
    }


    public static function register()
    {

        BasicRoute::add('/tualocms/page/scss/(?P<file>[\/.\w\d\-\_\.]+)' . '', function ($matches) {
            Sass::deliverFile($matches);
        }, ['get'], true, [], self::scope());
    }
}
