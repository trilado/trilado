<?php
/*
 * Copyright (c) 2012, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */


/**
 * A classe Registry guarda a instância das classes utilizadas no framework
 * 
 * @author		Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version		1
 *
 */ 
class Registry
{
	/**
	 * Guarda as instâncias das classes
	 * @var array 
	 */
	private $data = array();
	
	/**
	 * Guarda a instância da própria classe
	 * @var	Registry
	 */
	private static $instance = null;
	
	/**
	 * Construtor da classe, é privado para evitar multiplas instâncias
	 */
	private function __construct() { }
	
	/**
	 * Retorna uma instância da classe Registry (padrão Singleton)
	 * @return	Registry	retorna uma da própria classe
	 */
	public static function getInstance()
	{
		if(!self::$instance)
			self::$instance = new self();
		return self::$instance;
	}
	
	/**
	 * Define um registro de uma classe
	 * @param	string	$key	nome da classe
	 * @param	object	$value	instância da classe
	 */
	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}
	
	/**
	 * Retorna a instância de uma classe registrada
	 * @param	string	$key	nome da classe
	 * @return	object	retorna a instância de uma classe registrada
	 */
	public function get($key)
	{
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}
	
	/**
	 * Verifica a existência de um registro de uma classe
	 * @param	string	$key	nome da classe
	 * @return	boolean	retorna true caso o registro exista, no contrário retorna false
	 */
	public function has($key)
	{
		return isset($this->data[$key]);
	}
}