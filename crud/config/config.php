<?php
/*
 * Copyright (c) Trilado Team (triladophp.org)
 * All rights reserved.
 */

/**
 * Arquivo de configuração
 *
 */

/**
 * Define o tipo do debug
 */
Config::set('debug', array(
    'type' => 'local', //pode assumir os seguintes valores: off, local, network e all
    'query' => false, //pode assumir false, para desativar, ou um valor para a query ?debug=seu-valor-seguro
    'sql' => true,
));

/**
 * Tipo do drive do banco de dados, pode assumir os seguintes valores: mysql
 */
Config::set('database', array(
    'default' => array(
        'type' => 'mysql',
        'host' => 'localhost',
        'name' => 'trilado2',
        'user' => 'root',
        'pass' => '1234',
        'validate' => true,
    ),
));

/**
 * Define as configurações de cache
 */
Config::set('cache', array(
    'enabled' => false,
    'type' => 'file',
    'host' => 'localhost',
    'port' => '',
    'page' => false,
    'time' => 10,
));

Config::set('directories', array(
    'controller' => App::$root . 'app/controllers',
    'model' => App::$root . 'app/models',
    'helper' => App::$root . 'app/helpers',
    'vendor' => App::$root . 'app/vendors',
));
