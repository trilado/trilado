<?php

$root = dirname(__DIR__);
chdir($root);

require_once 'crud/config/bootstrap.php';
require_once 'crud/command/AbstractCommand.php';
require_once 'crud/command/SqlCommand.php';
require_once 'crud/command/ControllerCommand.php';
require_once 'crud/command/ViewCommand.php';

if (count($argv) < 2) {
	echo "Use: php crud.php [model].\n";
	exit(1);
}

(new ViewCommand)->run($argv);