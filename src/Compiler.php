<?php

namespace Tualo\Office\Sass;

use Tualo\Office\ExtJSCompiler\ICompiler;
use Tualo\Office\ExtJSCompiler\CompilerHelper;

class Compiler implements ICompiler
{


    public static function getFiles()
    {
        return CompilerHelper::getFiles(__DIR__, 'sass_css', 10000);
    }
}
