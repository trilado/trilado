<?php
/*
 * Copyright (c) 2011-2012, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */


/**
 * Classe de configuração
 * 
 * @author		Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version		1
 *
 */ 
class Config
{
	/**
	 * Guarda os valores das configurações
	 * @var	array
	 */
	private static $values = array();
	
	/**
	 * Construtor da classe, é privado para deixar criar uma instância da classe 
	 */
	private function __construct() {}
	
	/**
	 * Define o valor de uma configuração
	 * @param	string	$key	nome da configuração
	 * @param	mixed	$value	valor da configuração
	 * @return	void
	 */
	public static function set($key, $value)
	{
		self::$values[$key] = $value;
	}
	
	/**
	 * Retorna o valor de uma configuração
	 * @param	string	$key	nome da configuração
	 * @return	mixed			retorna o valor da configuração 
	 */
	public static function get($key)
	{
		if(isset(self::$values[$key]))
			return self::$values[$key];
		return null;
	}
}