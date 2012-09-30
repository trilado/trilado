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
class MemcacheCachesource extends Cachesource
{
	/**
	 * Guarda a instância da classe Memcache que contém a conexão com servidor
	 * @var	Memcache
	 */
	protected static $instance = null;
	
	/**
	 * Conecta com o servidor
	 * @return	Memcache	retorna uma instância de Memcache
	 */
	protected static function connect()
	{
		if(!self::$instance)
		{
			$config = Config::get('cache');
			self::$instance = new Memcache;
			self::$instance->connect($config['host'], $config['port']);
		}
		return self::$instance;
	}
	
	/**
	 * Escreve dados no cache
	 * @param	string	$key	chave em que será gravado o cache
	 * @param	mixed	$data	dados a serem gravados
	 * @param	int		$time	tempo, em minutos, que o cache existirá
	 * @return	boolean			retorna true se o cache for gravado com sucesso, no contrário, retorna false
	 */
	public function write($key, $data, $time = 1)
	{
		$memcached = self::connect();
		return $memcached->set(md5($key), $data, MEMCACHE_COMPRESSED, ($time * minute));
	}
	
	/**
	 * Ler e retorna os dados do cache
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	mixed			retorna os dados se o cache existir, no contrário retorna false (use !== false)
	 */
	public function read($key)
	{
		$memcached = self::connect();
		return $memcached->get(md5($key), MEMCACHE_COMPRESSED);
	}
	
	/**
	 * Remove um cache específico
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	boolean			retorna true se o cache foi removido com sucesso, no contrário retorna false
	 */
	public function delete($key)
	{
		$memcached = self::connect();
		return $memcached->delete(md5($key));
	}
	
	/**
	 * Remove todos os dados do cache
	 * @return	void 
	 */
	public function clear()
	{
		$memcached = self::connect();
		$memcached->flush();
	}

	/**
	 * Verifica se um cache existe
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	boolean			retorna true se o cache existir, no contrário retorna false 
	 */
	public function has($key)
	{
		return $this->read($key) !== false;
	}
}