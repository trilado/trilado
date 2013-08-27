<?php
/*
 * Copyright (c) Trilado Team (triladophp.org)
 * All rights reserved.
 */

	require_once 'libs/App.php';

	//calcula o endereço de root
	$root = str_replace('\\', '/', dirname(dirname(__FILE__)));
	if(substr($root, -1) != '/') $root = $root .'/';

	App::init($root);
	
	//importa o arquivo de erro
	require_once App::$rootCore . 'libs/Error.php';
	require_once App::$rootCore . 'libs/Debug.php';
	
	error_reporting(E_ALL);
	ini_set('display_errors', 0);
	
	set_error_handler(array('Error','handle'));
	register_shutdown_function(array('Error', 'shutdown'));
	
	//importa os arquivos iniciais
	require_once App::$rootCore . 'libs/Cache.php';
	require_once App::$rootCore . 'libs/Cachesource.php';
	require_once App::$rootCore . 'libs/Import.php';
	require_once App::$rootCore . 'libs/Route.php';
	require_once App::$rootCore . 'libs/Config.php';
	require_once App::$rootCore . 'libs/Module.php';
	require_once App::$root . 'app/config.php';
	require_once App::$root . 'app/routes.php';
	require_once App::$rootCore . 'constantes.php';
	require_once App::$rootCore . 'functions.php';
	
	//registra a função de autoload
	spl_autoload_register(array('Import', 'autoload'));
	
	//registra os diretórios padrão de arquivos de código fonte da framework
	Import::register(App::$rootCore . 'libs/', 'core');
	Import::register(App::$rootCore . 'libs/exceptions/', 'exception');
	Import::register(App::$rootCore . 'libs/datasource/', 'datasource');
	Import::register(App::$rootCore . 'libs/cachesource/', 'cachesource');
	Import::register(App::$rootCore . 'libs/vendors/');
	Import::register(App::$rootCore . 'libs/HTTP/');
	
	
	foreach(Config::get('directories') as $k => $d)
		Import::register($d, $k);
	
	//Module::add('app', 'app/');
	foreach(Config::get('modules') as $n => $p)
		Module::add($n, $p);
	Module::init();
	
	$url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	
	new App($url);
	
