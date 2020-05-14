<?php

use Nette\Diagnostics\Debugger;

require_once 'Nette/loader.php';
require_once 'PHPUnit/Autoload.php';
require_once __DIR__ . '/../src/automatons.php';

Debugger::enable(Debugger::DEVELOPMENT, FALSE);
Debugger::$strictMode = TRUE;
Debugger::$maxDepth = FALSE;
Debugger::$maxLen = FALSE;

$loader = new Nette\Loaders\RobotLoader;
$loader->setCacheStorage(new Nette\Caching\Storages\FileStorage(__DIR__ . '/temp'));
$loader->addDirectory(__DIR__ . '/classes');
$loader->register();

function id($a) { return $a; }
