<?php

namespace Tualo\Office\Sass\Routes;

use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\Route as BasicRoute;
use Tualo\Office\Sass\Import as SassImport;
use Tualo\Office\Sass\Sass;
use MatthiasMullie\Minify\CSS;
use Tualo\Office\Basic\Path;

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
            SassImport::import();
            App::result('success', true);
        }, ['get'], true, [], self::scope());


        BasicRoute::add('/scss/replaceimport' . '', function ($matches) {
            App::contenttype('application/json');
            SassImport::import(true);
            App::result('success', true);
        }, ['get'], true, [], self::scope());


        BasicRoute::add('/scss/compile' . '', function ($matches) {
            App::contenttype('application/json');

            if (($cmd = App::configuration('scss', 'cmd', false)) == false) throw new \Exception('scss cmd not found');

            $sql = 'select * from scss';
            $db = App::get('session')->getDB();
            $data = $db->direct($sql);
            foreach ($data as $row) {
                $filename = Path::join(Sass::getCompilerPath(), $row['filename']);
                $dirname = dirname($filename);
                if (!is_dir($dirname)) {
                    mkdir($dirname, 0777, true);
                }
                file_put_contents($filename, $row['content']);
            }

            $resfilename = Path::join(Sass::getCachePath(), 'bootstrap.css');
            if (!is_dir(dirname($resfilename))) {
                mkdir(dirname($resfilename), 0777, true);
            }

            $entryPoint = Path::join(Sass::getCompilerPath(),  'bootstrap.scss');
            if (file_exists(Path::join(Sass::getCompilerPath(), 'index.scss'))) $entryPoint = Path::join(Sass::getCompilerPath(), 'index.scss');
            exec($cmd . ' ' . $entryPoint . ' ' . $resfilename . ' 2>&1', $return, $res_code);
            App::result('return', $return);
            if ($res_code != 0) {
                App::result('return', $return);
                throw new \Exception('scss compile error');
            } else {
            }
            $minifier = new CSS($resfilename);
            $resfilename = Path::join(Sass::getCachePath(), 'bootstrap.min.css');
            $minifier->minify($resfilename);

            App::result('success', true);
        }, ['get'], true, [], self::scope());
    }
}
