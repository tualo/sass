<?php

namespace Tualo\Office\Sass;

use Tualo\Office\Basic\TualoApplication as App;


class ImportSCSS
{

    public static function rglob(string $patterns, $flags = GLOB_NOSORT): array
    {
        $result = glob($patterns, $flags);
        foreach ($result as $item) {
            if (is_dir($item)) {
                array_push($result, ...self::rglob($item . '/*', $flags));
            }
        }

        return $result;
    }

    public static function import(bool $replace = false, mixed $db = null, $path = '', $dbpathprefix = '')
    {
        if (is_null($db)) $db = App::get('session')->getDB();

        if ($path == '') $path = (__DIR__) . '/scss';
        $files = self::rglob($path . '/*', GLOB_NOSORT | GLOB_BRACE);
        $files = array_map(function ($file) use ($path) {
            $file = str_replace($path . '/', '', $file);
            return $file;
        }, $files);

        if (strlen($dbpathprefix) > 0 && (strrev($dbpathprefix)[0] != '/')) $dbpathprefix = $dbpathprefix . '/';

        foreach ($files as $file) {
            if (!is_dir($path . '/' . $file)) {
                $data = file_get_contents($path . '/' . $file);
                $type = 'insert ignore';
                if ($replace) $type = 'replace';
                $db->direct($type . '
                    into scss (
                        filename,
                        content
                    ) values (
                        {filename},
                        {content}
                    )
                ', [
                    'filename' => $dbpathprefix . $file,
                    'content' => $data
                ]);
            }
        }
    }
}
