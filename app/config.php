<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Arquivo de configuração
 * 
 */

/**
 * Define o tipo do debug, pode assumir os seguintes valores: off, local, network e all
 * @var	string
 */
Config::set('debug', 'local');

/**
 * Tipo do drive do banco de dados, pode assumir os seguintes valores: mysql
 * @var	string
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
 * Master Page padrão
 * @var	string
 */
Config::set('default_master', 'template');

/**
 * Controller padrão
 * @var	string
 */
Config::set('default_controller', 'Home');

/**
 * Action padrão
 * @var	string
 */
Config::set('default_action', 'index');

/**
 * Página de login
 * @var	string
 */
Config::set('default_login', '~/admin');

/**
 * Charset padrão
 * @var	string
 */
Config::set('charset', 'UTF-8');

/**
 * Linguagem padrão
 * @var	string
 */
Config::set('default_lang', 'pt-br');

/**
 * Chave de segurança (deve ser alterada)
 * @var	string
 */
Config::set('salt', 'ad$sfGFH33F132sAasds!@xcz!z\x*(f^`{`lda\\A|zahkl.m,kH2?Ed');

/**
 * Define se as requisições AJAX devem retornar automaticamente conteúdo em JSON
 * @var	boolean 
 */
Config::set('auto_ajax', false);

/**
 * Define se actions acessadas com .xml devem retorna automaticamente conteúdo em XML
 * @var	boolean
 */
Config::set('auto_dotxml', false);

/**
 * Define se actions acessadas com .json devem retorna automaticamente conteúdo em JSON
 * @var	boolean
 */
Config::set('auto_dotjson', false);

Config::set('cache', array(
	'type' => 'file',
	'host' => 'localhost',
	'port' => '',
	'time' => 1
));

//Import::register($dir); //Registrar diretórios de arquivos de código fonte, para autoload.