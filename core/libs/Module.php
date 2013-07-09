<?php
/*
 * Copyright (c) 2012-2013, Jackson Gomes <jackson.souza@gmail.com>
 * All rights reserved.
 */

/**
 * 
 * @author	Jackson Gomes <jackson.souza@gmail.com>
 * @author	Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version	0.2
 */
class Module
{
	/**
	 * Guarda os módulos
	 * 
	 * @var	array
	 */
	private static $collection = array();

	/**
	 * Adiciona um módulo
	 * @param	string	$name	nome do módulo que será utilizado na URL
	 * @param	string	$path	caminho, em disco, do módulo na aplicação
	 * @return	void
	 */
	public static function add($name, $path)
	{
		self::$collection[strtolower($name)] = $path;
	}

	/**
	 * Remove um módulo
	 * @param	string	$name	nome do módulo que será utilizado na URL
	 * @return	void
	 */
	public static function remove($name)
	{
		unset(self::$collection[strtolower($name)]);
	}

	/**
	 * Verifica se um módulo existe e está ativo
	 * 
	 * @param	string	$name	nome do módulo
	 * @return	boolean			retorna true se o módulo existir
	 */
	public static function exists($name)
	{
		return isset(self::$collection[strtolower($name)]);
	}

	/**
	 * Carrega os módulos ativos
	 * @return	void
	 */
	public static function init()
	{
		foreach (self::$collection as $m)
		{
			if (file_exists($m . 'init.php'))
				require_once $m . 'init.php';
		}
	}
	
	/**
	 * Obtem o caminho de um módulo pelo nome
	 * 
	 * @param	string	$name	nome do módulo
	 * @return	string			retorna o caminho, em disco, do módulo
	 */
	public static function path($name)
	{
		if(isset(self::$collection[$name]))
			return trim(self::$collection[$name], '/') . '/';
	}
}