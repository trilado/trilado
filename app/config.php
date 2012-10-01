<?php
/*
 * Copyright (c) 2011-2012, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Arquivo de configuração
 * 
 */

/**
 * Define o tipo do debug, pode assumir os seguintes valores: off, local, network e all
 */
Config::set('debug', 'local');

/**
 * Tipo do drive do banco de dados, pode assumir os seguintes valores: mysql
 */
Config::set('database', array(
	'default' => array(
		'type' => 'mysql',
		'host' => 'localhost',
		'name' => 'trilado2',
		'user' => 'root',
		'pass' => ''
	)
));

/**
 * Define se o framework irá validar automaticamente os models
 */
Config::set('auto_validate', true);

/**
 * Master Page padrão
 */
Config::set('default_master', 'template');

/**
 * Controller padrão
 */
Config::set('default_controller', 'Home');

/**
 * Action padrão
 */
Config::set('default_action', 'index');

/**
 * Página de login
 */
Config::set('default_login', '~/admin');

/**
 * Charset padrão
 */
Config::set('charset', 'UTF-8');

/**
 * Linguagem padrão
 */
Config::set('default_lang', 'pt-br');

/**
 * Chave de segurança (deve ser alterada)
 */
Config::set('salt', 'ad$sfGFH33F132sAasds!@xcz!z\x*(f^`{`lda\\A|zahkl.m,kH2?Ed');

/**
 * Define se as requisições AJAX devem retornar automaticamente conteúdo em JSON
 */
Config::set('auto_ajax', false);

/**
 * Define se actions acessadas com .xml devem retorna automaticamente conteúdo em XML
 */
Config::set('auto_dotxml', false);

/**
 * Define se actions acessadas com .json devem retorna automaticamente conteúdo em JSON
 */
Config::set('auto_dotjson', false);

/**
 * Define as configurações de cache
 */
Config::set('cache', array(
	'type'		=> 'file',
	'host'		=> 'localhost',
	'port'		=> '',
	'page'		=> true,
	'time'		=> 10
));

//Import::register($dir); //Registrar diretórios de arquivos de código fonte, para autoload.