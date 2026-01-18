<?php

namespace Tualo\Office\Sass\Commands;

use Tualo\Office\Basic\ICommandline;
use Tualo\Office\Basic\CommandLineInstallSQL;

class Install extends CommandLineInstallSQL  implements ICommandline
{
    public static function getDir(): string
    {
        return dirname(__DIR__, 1);
    }
    public static $shortName  = 'scss';
    public static $files = [
        'install/ddl' => 'setup ddl',
        'install/addcommand' => 'setup addcommand',
        'install/scss' => 'setup scss',
        'install/scss.ds' => 'setup scss.ds'
    ];
}
