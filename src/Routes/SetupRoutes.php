<?php

namespace Tualo\Office\Sass\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Basic\IRoute;
use Tualo\Office\Sass\ImportSCSS;
use MatthiasMullie\Minify\CSS;

class SetupRoute extends \Tualo\Office\Basic\RouteWrapper
{
    public static function scope(): string
    {
        return 'scss.setup.route';
    }

    public static function register()
    {
        BasicRoute::add('/scss/import' . '', function ($matches) {
            App::contenttype('application/json');
            ImportSCSS::import();
            App::result('success', true);
        }, ['get'], true, [], self::scope());


        BasicRoute::add('/scss/replaceimport' . '', function ($matches) {
            App::contenttype('application/json');
            ImportSCSS::import(true);
            App::result('success', true);
        }, ['get'], true, [], self::scope());


        BasicRoute::add('/scss/compile' . '', function ($matches) {
            App::contenttype('application/json');
            $cachePath = implode(DIRECTORY_SEPARATOR, [App::get('basePath') . '/' . 'cache']);
            $cachePath = App::configuration('scss', 'cache_path', $cachePath);

            if (($cmd = App::configuration('scss', 'cmd', false)) == false) throw new \Exception('scss cmd not found');

            $sql = 'select * from scss';
            $db = App::get('session')->getDB();
            $data = $db->direct($sql);
            foreach ($data as $row) {
                $filename = implode(DIRECTORY_SEPARATOR, [App::get('tempPath'), 'scss', $row['filename']]);
                $dirname = dirname($filename);
                if (!is_dir($dirname)) {
                    mkdir($dirname, 0777, true);
                }
                file_put_contents($filename, $row['content']);
            }

            $resfilename = implode(DIRECTORY_SEPARATOR, [$cachePath, 'bootstrap.css']);
            if (!is_dir(dirname($resfilename))) {
                mkdir(dirname($resfilename), 0777, true);
            }

            $entryPoint = implode(DIRECTORY_SEPARATOR, [App::get('tempPath'), 'scss', 'bootstrap.scss']);
            if (file_exists(implode(DIRECTORY_SEPARATOR, [App::get('tempPath'), 'scss', 'index.scss']))) $entryPoint = implode(DIRECTORY_SEPARATOR, [App::get('tempPath'), 'scss', 'index.scss']);
            exec($cmd . ' ' . $entryPoint . ' ' . $resfilename . ' 2>&1', $return, $res_code);
            App::result('return', $return);
            if ($res_code != 0) {
                App::result('return', $return);
                throw new \Exception('scss compile error');
            } else {
            }
            $minifier = new CSS($resfilename);
            $resfilename = implode(DIRECTORY_SEPARATOR, [$cachePath, 'bootstrap.min.css']);
            $minifier->minify($resfilename);

            App::result('success', true);
        }, ['get'], true, [], self::scope());
    }
}
