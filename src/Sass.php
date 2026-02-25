<?php

namespace Tualo\Office\Sass;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\Path;
use MatthiasMullie\Minify\CSS;

class Sass
{
    public static function getCachePath(): string
    {
        $cachePath = Path::join(App::get('basePath'), 'cache');
        $cachePath = App::configuration('scss', 'cache_path', $cachePath);
        return $cachePath;
    }

    public static function getCompilerPath(): string
    {
        $compilerPath = Path::join(App::get('tempPath'), 'scss');
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
            self::compile(str_replace('.min', '', str_replace('.css', '', $matches['file'])));
            App::etagFile(Path::join($dir, $matches['file']), true);
            BasicRoute::$finished = true;
            http_response_code(200);
        } else if (file_exists(Path::join(dirname(__DIR__, 2), 'lib', $matches['file']))) {
            App::etagFile(Path::join(dirname(__DIR__, 2), 'lib', $matches['file']), true);
            BasicRoute::$finished = true;
            http_response_code(200);
        }
    }



    public static function compile(string $fileName)
    {
        if (($cmd = App::configuration('scss', 'cmd', false)) == false) throw new \Exception('scss cmd not found');

        $sql = 'select * from scss order by filename';
        $db = App::get('session')->getDB();
        $data = $db->direct($sql);
        $etag = "";
        foreach ($data as $row) {
            $filename = Path::join(Sass::getCompilerPath(), $row['filename']);
            $dirname = dirname($filename);
            if (!is_dir($dirname)) {
                mkdir($dirname, 0777, true);
            }
            file_put_contents($filename, $row['content']);
            $etag = md5($etag . md5($row['content']));
        }

        $resfilename = Path::join(Sass::getCachePath(), $fileName);
        if (!is_dir(dirname($resfilename))) {
            mkdir(dirname($resfilename), 0777, true);
        }

        if (file_exists(Path::join(Sass::getCompilerPath(), $resfilename . '.etag'))) {
            $old_etag = file_get_contents(Path::join(Sass::getCompilerPath(), $resfilename . '.etag'));
            if ($old_etag == $etag && file_exists($resfilename)) {
                return;
            }
        }

        if (!file_exists(Path::join(Sass::getCompilerPath(), $fileName . '.scss')))
            throw new \Exception('scss entry point not found');

        $entryPoint = Path::join(Sass::getCompilerPath(), $fileName . '.scss');
        exec($cmd . ' ' . $entryPoint . ' ' . $resfilename . ' 2>&1', $return, $res_code);
        file_put_contents($resfilename . '.etag', $etag);

        App::result('return', $return);
        if ($res_code != 0) {
            App::result('return', $return);
            throw new \Exception('scss compile error');
        } else {
        }
        $minifier = new CSS($resfilename);
        $resfilename = Path::join(Sass::getCachePath(), $fileName . '.min.css');
        $minifier->minify($resfilename);
    }
}
