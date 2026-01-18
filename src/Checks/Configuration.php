<?php

namespace Tualo\Office\Sass\Checks;

use Tualo\Office\Basic\Middleware\Session;
use Tualo\Office\Basic\PostCheck;
use Tualo\Office\Basic\TualoApplication as App;


class Configuration  extends PostCheck
{

    public static function test(array $config)
    {
        $clientdb = App::get('clientDB');
        if (is_null($clientdb)) return;

        if (($cmd = App::configuration('scss', 'cmd', false)) == false) {
            PostCheck::formatPrintLn(['red'], "\tscss cmd not found");
            PostCheck::formatPrintLn(['blue'], "\tcall `./tm configuration --section scss --key cmd --value $(which sass)`");
        } else {
            exec($cmd . ' --version', $output, $return_var);
            if ($return_var != 0) {
                PostCheck::formatPrintLn(['red'], "\tscss cmd *$cmd* is not callable ($return_var), try `npm install -g sass`");
            } else {
                PostCheck::formatPrintLn(['green'], "\tscss version: " . implode(' ', $output));
            }
        }
    }
}
