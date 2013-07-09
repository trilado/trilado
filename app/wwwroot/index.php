<?php
/*
 * Copyright (c) 2011-2013, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


	//calcula o endereço de root
	$root = str_replace('\\', '/', dirname(dirname(dirname(__FILE__))));
	if(substr($root, -1) != '/') $root = $root .'/';
	
	//define as constantes de root
	define('root', $root);
	define('root_virtual', str_replace($_SERVER['DOCUMENT_ROOT'], '', root));
	define('wwwroot', root . 'app/wwwroot/');
	
	define('ROOT', $root);
	define('ROOT_VIRTUAL', str_replace($_SERVER['DOCUMENT_ROOT'], '', ROOT));
	define('WWWROOT', ROOT . 'app/wwwroot/');
	
	//importa o arquivo de erro
	require_once ROOT . 'core/libs/Error.php';
	require_once ROOT . 'core/libs/Debug.php';
	
	error_reporting(E_ALL);
	ini_set('display_errors', 0);
	
	set_error_handler(array('Error','handle'));
	register_shutdown_function(array('Error', 'shutdown'));
	
	//importa os arquivos iniciais
	require_once ROOT . 'core/libs/Cache.php';
	require_once ROOT . 'core/libs/Cachesource.php';
	require_once ROOT . 'core/libs/Import.php';
	require_once ROOT . 'core/libs/Route.php';
	require_once ROOT . 'core/libs/Config.php';
	require_once ROOT . 'core/libs/Module.php';
	require_once ROOT . 'app/config.php';
	require_once ROOT . 'app/routes.php';
	require_once ROOT . 'core/constantes.php';
	require_once ROOT . 'core/functions.php';
	
	//registra a função de autoload
	spl_autoload_register(array('Import', 'autoload'));
	
	//registra os diretórios padrão de arquivos de código fonte da framework
	Import::register('core/libs/');
	Import::register('core/libs/exceptions/');
	Import::register('core/libs/datasource/');
	Import::register('core/libs/cachesource/');
	Import::register('core/libs/vendors/');
	Import::register('core/libs/HTTP/');
	
	
	foreach(Config::get('directories') as $d)
		Import::register($d);
	
	//Module::add('app', 'app/');
	foreach(Config::get('modules') as $n => $p)
		Module::add($n, $p);
	Module::init();
	
	Import::core('App');
	
	$url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	
	new App($url);
	
