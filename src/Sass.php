<?php

namespace Tualo\Office\Sass;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\Path;

class Sass
{
    public static function getCachePath(): string
    {
        $cachePath = Path::join([App::get('basePath'), 'cache']);
        $cachePath = App::configuration('scss', 'cache_path', $cachePath);
        return $cachePath;
    }

    public static function getCompilerPath(): string
    {
        $compilerPath = Path::join([App::get('tempPath'), 'scss']);
        $compilerPath = App::configuration('scss', 'compiler_path', $compilerPath);
        return $compilerPath;
    }


    public static function deliverFile($matches)
    {
        $dir = self::getCachePath();
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
}
