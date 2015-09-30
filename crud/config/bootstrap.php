<?php

/*
* Copyright (c) Trilado Team (triladophp.org)
* All rights reserved.
*/


//importa o arquivo de erro
require_once 'core/libs/App.php';
require_once 'core/libs/Error.php';
require_once 'core/libs/Debug.php';

$root = str_replace('\\', '/', $root);
if(substr($root, -1) != '/') 
	$root = $root .'/';

App::init($root);

error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

set_error_handler(array('Error','handle'));
register_shutdown_function(array('Error', 'shutdown'));

//importa os arquivos iniciais
require_once 'core/libs/Cache.php';
require_once 'core/libs/Cachesource.php';
require_once 'core/libs/Import.php';
require_once 'core/libs/Route.php';
require_once 'core/libs/Config.php';
require_once 'core/libs/Module.php';
require_once 'core/functions.php';
require_once 'core/constantes.php';

$config = 'app/config.local.php';
if(file_exists('crud/config/config.php')) {
	$config = 'crud/config/config.php';
}
require_once $config;

//registra a função de autoload
spl_autoload_register(array('Import', 'autoload'));

//registra os diretórios padrão de arquivos de código fonte da framework
Import::register('core/libs/', 'core');
Import::register('core/libs/exceptions/', 'exception');
Import::register('core/libs/datasource/', 'datasource');
Import::register('core/libs/cachesource/', 'cachesource');
Import::register('core/libs/HTTP/');

if(is_array(Config::get('directories'))) {
	foreach(Config::get('directories') as $k => $d) {
		Import::register($d, $k);
	}
}