<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


	//calcula o endereço de root
	$root = str_replace('\\', '/', dirname(dirname(dirname(__FILE__))));
	if(substr($root, -1) != '/') $root = $root .'/';
	
	//define as variáveis de root
	define('root', $root);
	define('root_virtual', str_replace($_SERVER['DOCUMENT_ROOT'], '', root));
	define('wwwroot', root . 'app/wwwroot/');
	
	//importa os arquivos iniciais
	require_once root . 'core/libs/Import.php';
	require_once root . 'core/libs/Route.php';
	require_once root . 'app/config.php';
	require_once root . 'app/routes.php';
	require_once root . 'core/constantes.php';
	require_once root . 'core/functions.php';
	
	Import::core('App');
	
	$url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	
	new App($url);
	
