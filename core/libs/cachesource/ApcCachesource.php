<?php

/*
 * Copyright (c) 2012, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */

/**
 * Classe para manipulação de cache utilizando o APC. Para utilizá-la é necessário
 * a instalação do APC.
 * 
 * @author		Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @author		Diego Oliveia <diegopso2@gmail.com>
 * @version		1.2
 *
 */
class ApcCachesource extends Cachesource
{
	/**
	 * Guarda uma instância da própria classe
	 * @var	ApcCachesource 
	 */
	private static $instance = null;

	/**
	 * Identificador de grupos de cache.
	 */
	const GROUP_ID = 'TRILADO_APC_GROUP';

	/**
	 * Construtor da classe, é protegido para não ser instanciada 
	 */
	protected function __construct()
	{
		
	}

	/**
	 * Método para instanciação do classe
	 * @return	ApcCachesource		retorna a instância da classe ApcCachesource
	 */
	public static function getInstance()
	{
		if (!self::$instance)
			self::$instance = new self();
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
		$file = array();
		$file['time'] = time() + ($time * minute);
		$file['data'] = $data;

		return apc_store(md5($key), $file);
	}

	/**
	 * Ler e retorna os dados do cache
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	mixed			retorna os dados se o cache existir, no contrário retorna false (use !== false)
	 */
	public function read($key)
	{
		$data = apc_fetch(md5($key));
		if ($data !== false)
		{
			if (isset($data['time']) && isset($data['data']) && ((int) $data['time']) > time())
				return $data['data'];
		}
		return false;
	}

	/**
	 * Remove um cache específico
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	boolean			retorna true se o cache foi removido com sucesso, no contrário retorna false
	 */
	public function delete($key)
	{
		return apc_delete(md5($key));
	}

	/**
	 * Remove todos os dados do cache
	 * @return	void 
	 */
	public function clear()
	{
		return apc_clear_cache('user');
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