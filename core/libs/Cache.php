<?php
/*
 * Copyright (c) 2012, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */


/**
 * Classe para manipulação de Cache
 * 
 * @author		Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version		1.1
 *
 */ 
class Cache
{
	/**
	 * Guarda a instância da classe de manipulação do cache
	 * @var	Cachesource
	 */
	private static $instance = null;
	
	/**
	 * Construtor da classe, é privado para a classe não ser instanciada 
	 */
	public function __construct() {}
	
	/**
	 * Método que chama a classe de manipulação do cache de acordo com a configuração
	 * @return	Cachesource		retorna uma instância do Cachesource de acordo com a configuração
	 */
	public static function factory()
	{
		if(!self::$instance)
		{
			$config = Config::get('cache');
			
			$class = ucfirst(strtolower($config['type'])) . 'Cachesource';
			Import::load('cachesource', array($class));
			self::$instance = call_user_func(array($class, 'getInstance'));
		}
		return self::$instance;
	}
}