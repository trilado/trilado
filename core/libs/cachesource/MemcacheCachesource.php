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
 * @author		Diego Oliveia <diegopso2@gmail.com>
 * @version		1.2
 *
 */
class MemcacheCachesource extends Cachesource
{
	/**
	 * Guarda uma instância da própria classe
	 * @var	MemcacheCachesource 
	 */
	protected static $instance = null;

	/**
	 * Guarda a instância da classe Memcache que contém a conexão com servidor
	 * @var	Memcache
	 */
	protected $memcached = null;

	/**
	 * Identificador de grupos de cache. 
	 */
	const GROUP_ID = 'TRILADO_MEMCACHE_GROUP';

	/**
	 * Construtor da classe, é protegido para não ser instanciada 
	 */
	protected function __construct()
	{
		$this->connect();
	}

	/**
	 * Método para instanciação do classe
	 * @return	MemcacheCachesource		retorna a instância da classe MemcacheCachesource
	 */
	public static function getInstance()
	{
		if (!self::$instance)
			self::$instance = new self();
		return self::$instance;
	}

	/**
	 * Conecta com o servidor
	 * @return	void
	 */
	protected function connect()
	{
		$config = Config::get('cache');
		$this->memcached = new Memcache;
		$this->memcached->connect($config['host'], $config['port']);
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
		return $this->memcached->set(md5($key), $data, MEMCACHE_COMPRESSED, ($time * minute));
	}

	/**
	 * Ler e retorna os dados do cache
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	mixed			retorna os dados se o cache existir, no contrário retorna false (use !== false)
	 */
	public function read($key)
	{
		return $this->memcached->get(md5($key), MEMCACHE_COMPRESSED);
	}

	/**
	 * Remove um cache específico
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	boolean			retorna true se o cache foi removido com sucesso, no contrário retorna false
	 */
	public function delete($key)
	{
		return $this->memcached->delete(md5($key));
	}

	/**
	 * Remove todos os dados do cache
	 * @return	void 
	 */
	public function clear()
	{
		$this->memcached->flush();
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