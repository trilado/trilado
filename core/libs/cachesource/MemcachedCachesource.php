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
 * @version		1.1
 *
 */ 
class MemcachedCachesource extends MemcacheCachesource
{	
	/**
	 * Conecta com o servidor
	 * @return	void
	 */
	protected function connect()
	{
		$config = Config::get('cache');
		$this->memcached = new Memcached();
		$this->memcached->addServer($config['host'], $config['port']);
	}
}