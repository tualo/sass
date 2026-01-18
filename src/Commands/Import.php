<?php

namespace Tualo\Office\Sass\Commands;

use Garden\Cli\Cli;
use Garden\Cli\Args;
use Tualo\Office\Basic\ICommandline;
use Tualo\Office\ExtJSCompiler\Helper;
use Tualo\Office\Basic\TualoApplication as App;
use Tualo\Office\Basic\PostCheck;
use Tualo\Office\Sass\ImportSCSS;

class Import implements ICommandline
{

    public static function getCommandName(): string
    {
        return 'import-scss';
    }

    public static function setup(Cli $cli)
    {
        $cli->command(self::getCommandName())
            ->description('import basic scss files')
            ->opt('client', 'only use this client', true, 'string')
            ->opt('force', 'force import', false, 'boolean')
            ->opt('path', 'path to import from', '', 'string')
            ->opt('prefix', 'prefix in db', '', 'string');
    }


    public static function setupClients(string $msg, string $clientName, Args $args, callable $callback)
    {
        $_SERVER['REQUEST_URI'] = '';
        $_SERVER['REQUEST_METHOD'] = 'none';
        App::run();

        $session = App::get('session');
        $sessiondb = $session->db;
        $dbs = $sessiondb->direct('select username db_user, password db_pass, id db_name, host db_host, port db_port from macc_clients ');
        foreach ($dbs as $db) {
            if (($clientName != '') && ($clientName != $db['db_name'])) {
                continue;
            } else {
                App::set('clientDB', $session->newDBByRow($db));
                PostCheck::formatPrint(['blue'], $msg . '(' . $db['db_name'] . '):  ');
                $callback($args);
                PostCheck::formatPrintLn(['green'], "\t" . ' done');
            }
        }
    }

    public static function run(Args $args)
    {
        $install = function (Args $args) {


            ImportSCSS::import($args->getOpt('force', false), App::get('clientDB'), $args->getOpt('path', ''), $args->getOpt('prefix', ''));
        };
        $clientName = $args->getOpt('client');
        if (is_null($clientName)) $clientName = '';
        self::setupClients("", $clientName, $args, $install);
    }
}
