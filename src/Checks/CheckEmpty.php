<?php

namespace Tualo\Office\Sass\Checks;

use Tualo\Office\Basic\Middleware\Session;
use Tualo\Office\Basic\PostCheck;
use Tualo\Office\Basic\TualoApplication as App;


class CheckEmpty  extends PostCheck
{

    public static function test(array $config)
    {
        $clientdb = App::get('clientDB');
        if (is_null($clientdb)) return;
        try {
            $res = App::get('clientDB')->direct('select * from scss');
        } catch (\Exception $e) {
            $res = [];
        }
        if (count($res) == 0) {
            PostCheck::formatPrintLn(['red'], 'scss is empty');
            PostCheck::formatPrintLn(['blue'], 'please run the following command: `./tm import-scss --client ' . $clientdb->dbname . '`');
        } else {
            PostCheck::formatPrintLn(['green'], 'scss is not empty');
        }
    }
}
