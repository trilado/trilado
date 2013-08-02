<?php

/*
 * Copyright (c) 2012-2013, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */

/**
 * Classe modelo, abstrata, para manipulação de cache
 * 
 * @author		Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @author		Diego Oliveia <diegopso2@gmail.com>
 * @version		1.4
 *
 */
abstract class Cachesource
{

	/**
	 * Construtor da classe, é privado para garantir a existência de uma única instância da classe 
	 */
	abstract protected function __construct();

	/**
	 * Método para instanciação do classe
	 * @return	Cachesource		retorna a instância da classe Cachesource
	 */
	//abstract public function getInstance();

	/**
	 * Escreve dados no cache
	 * @param	string	$key	chave em que será gravado o cache
	 * @param	mixed	$data	dados a serem gravados
	 * @param	int		$time	tempo, em minutos, que o cache existirá
	 * @return	boolean			retorna true se o cache for gravado com sucesso, no contrário, retorna false
	 */
	abstract public function write($key, $data, $time = 1);

	/**
	 * Ler e retorna os dados do cache
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	mixed			retorna os dados se o cache existir, no contrário retorna false (use !== false)
	 */
	abstract public function read($key);

	/**
	 * Remove um cache específico
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	boolean			retorna true se o cache foi removido com sucesso, no contrário retorna false
	 */
	abstract public function delete($key);

	/**
	 * Remove todos os dados do cache
	 * @return	void 
	 */
	abstract public function clear();

	/**
	 * Verifica se um cache existe
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	boolean			retorna true se o cache existir, no contrário retorna false 
	 */
	abstract public function has($key);

	/**
	 * Retorna um array com as chaves contidas em um grupo de cache. 
	 * @param	string	$groupName	O nome do grupo a retornar.
	 */
	public function getGroup($groupName)
	{
		$groupName = 'Trilado.Cache.Group.' . $groupName;
		if ($this->has($groupName))
			return $this->read($groupName);
		return array();
	}
	
	public function hasGroup($name)
	{
		return $this->has('Trilado.Cache.Group.' . $name);
	}

	/**
	 * Adiciona uma chave de cache a um grupo de cache.
	 * @param	string	$groupName	O nome do grupo para adicionar a chave.
	 * @param	string	$key		A chave a ser adicionada.
	 */
	public function addToGroup($groupName, $key)
	{
		$groupName = 'Trilado.Cache.Group.' . $groupName;
		if ($this->has($groupName))
			$group = $this->read($groupName);
		else
			$group = array();
		$group[md5($key)] = $key;
		$this->write($groupName, $group, YEAR);
	}

	/**
	 * Deleta todas as informações de cahce que estão em um grupo.
	 * @param	string	$groupName	O nome do grupo a ser apagado.
	 */
	public function deleteGroup($groupName)
	{
		$groupName = 'Trilado.Cache.Group.' .  $groupName;
		if ($this->has($groupName))
		{
			$group = $this->read($groupName);

			foreach ($group as $k)
				$this->delete($k);
			$this->delete($groupName);
		}
	}
	
	public function hasOnGroup($name, $key)
	{
		$name = 'Trilado.Cache.Group.' . $name;
		if($this->has($name))
		{
			$group = $this->read($name);
			return isset($group[md5($key)]);
		}
		return false;
	}
}