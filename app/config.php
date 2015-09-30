<?php
/*
 * Copyright (c) Trilado Team (triladophp.org)
 * All rights reserved.
 */


/**
 * Arquivo de configuração
 * 
 */

require_once 'config.local.php';

/**
 * Master Page padrão
 */
Config::set('default_master', 'template');

/**
 * Controller padrão
 */
Config::set('default_controller', 'Home');

/**
 * Controller padrão para páginas de erro. Defina como NULL para não utilizar controler de erro
 */
Config::set('error_controller', null);

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
 * Formato padrão da data.
 */
Config::set('date_format', 'd/m/Y');

/**
 * Chave de segurança (deve ser alterada)
 */
Config::set('salt', 'aaddf775&gflkrf][335re$%T]efv4d');

/**
 * Define se as requisições via dispositivo móvel irão carregar os templates específicos, se existirem, para versão móvel
 */
Config::set('auto_mobile', false);

/**
 * Define se as requisições via tablet irão carregar os templates  específicos, se existirem, para versão tablet
 */
Config::set('auto_tablet', false);

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
 * Registrar diretórios de arquivos de código fonte, para autoload 
 */
Config::set('directories', array(
	'controller' 	=> App::$root . 'app/controllers',
	'model' 		=> App::$root . 'app/models',
	'helper' 		=> App::$root . 'app/helpers',
	'vendor' 		=> App::$root . 'app/vendors',
));

/**
 * Registrar diretórios de arquivos de código fonte, para autoload 
 */
Config::set('modules', array(
	'example' 		=> App::$root . 'app/modules/example/',
));