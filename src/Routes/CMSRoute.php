<?php

namespace Tualo\Office\Sass\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\Path;

class CMSRoute extends \Tualo\Office\Basic\RouteWrapper
{
    public static function scope(): string
    {
        return 'scss.cms.route';
    }

    public static function deliverFile($matches)
    {
        $dir = Path::join(App::get('basePath'), 'scss_build');
        if (
            BasicRoute::checkDoubleDots($matches, 'file', 'Path contains ".."') &&
            file_exists(Path::join($dir, $matches['file']))
        ) {
            App::etagFile(Path::join($dir, $matches['file']), true);
            BasicRoute::$finished = true;
            http_response_code(200);
        } else if (file_exists(Path::join(dirname(__DIR__, 2), 'lib', $matches['file']))) {
            App::etagFile(Path::join(dirname(__DIR__, 2), 'lib', $matches['file']), true);
            BasicRoute::$finished = true;
            http_response_code(200);
        }
    }


    public static function register()
    {

        BasicRoute::add('/tualocms/page/scss/(?P<file>[\/.\w\d\-\_\.]+)' . '', function ($matches) {
            CMSRoute::deliverFile($matches);
        }, ['get'], true, [], self::scope());
    }
}
