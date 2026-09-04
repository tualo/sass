<?php

namespace Tualo\Office\Sass\Middlewares;

use Tualo\Office\Basic\TualoApplication;
use Tualo\Office\Basic\IMiddleware;

class CSS implements IMiddleware
{
    public static function register()
    {
        TualoApplication::use('sass_css_middleware', function () {
            try {
                $fileList = [];
                $files = trim(TualoApplication::configuration('scss', 'backend_files', ''));
                if (strlen($files) > 0) {
                    $fileList = explode(' ', $files);
                }
                foreach ($fileList as $file) {
                    $file = trim($file);
                    TualoApplication::stylesheet("./scss/" . $file . ".css", 100000);
                }
            } catch (\Exception $e) {
                TualoApplication::set('maintanceMode', 'on');
                TualoApplication::addError($e->getMessage());
            }
        }, -100); // should be one of the last
    }
}
