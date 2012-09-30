<?php
/*
 * Copyright (c) 2012, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */


/**
 * Classe para manipulação de cache utilizando o Memcached. Para utilizá-la é necessário
 * a instalação do Memcached.
 * 
 * @author		Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version		1
 *
 */ 
class MemcachedCachesource extends MemcacheCachesource
{	
	/**
	 * Conecta com o servidor
	 * @return	Memcache	retorna uma instância de Memcache
	 */
	protected static function connect()
	{
		if(!self::$instance)
		{
			$config = Config::get('cache');

			self::$instance = new Memcached();
			self::$instance->addServer($config['host'], $config['port']);
		}
		return self::$instance;
	}
}