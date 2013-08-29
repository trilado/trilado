<?php
/*
 * Copyright (c) Trilado Team (triladophp.org)
 * All rights reserved.
 */

/**
 * 
 * @author	Jackson Gomes <jackson.souza@gmail.com>
 * @author	Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @author	Diego Oliveira <diegopso2@gmail.com>
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
		self::$collection[$name] = $path;
	}

	/**
	 * Remove um módulo
	 * @param	string	$name	nome do módulo que será utilizado na URL
	 * @return	void
	 */
	public static function remove($name)
	{
		unset(self::$collection[$name]);
	}

	/**
	 * Verifica se um módulo existe e está ativo
	 * 
	 * @param	string	$name	nome do módulo
	 * @return	boolean			retorna true se o módulo existir
	 */
	public static function exists($name)
	{
		return isset(self::$collection[$name]);
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
			{
				Import::register($m . 'controllers/');
				Import::register($m . 'models/');
				Import::register($m . 'vendors/');
				Import::register($m . 'helpers/');

				require_once $m . 'init.php';
			}
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

	/**
	 * Guarda o histórico dos pais das sub-requisições
	 * @var array
	 */
	public static $requestStack = array();

	/**
	 * Remove a primeira requisição do topo da pilha de subrequisições e a retorna.
	 * @return array 	A requisição desejada
	 */
	public static function popRequest()
	{
		return array_shift(self::$requestStack);
	}

	/**
	 * Adiciona uma requisição à pilha de subrequisições.
	 * @param 	array 	Requisição a ser adicionada
	 */
	public static function pushRequest($request)
	{
		array_unshift(self::$requestStack, $request);
	}

	/**
	 * Retorna o primeiro elemento da pilha de subrequisições.
	 * @return 	array 	A requisição desejada
	 */
	public static function topRequest()
	{
		$request = self::popRequest();
		self::pushRequest($request);
		return $request;
	}

	/**
	 * Executa uma subrequisição dentro da requisição atual.
	 * @param 	string 	$url 	url para executar a subrequisição (ex.: /post/view/5)
	 * @return 	string 	HTML gerado ao executar a instrução
	 */
	public static function run($url)
	{
		ob_start();

		array_unshift(self::$requestStack, array(
			'args' 		=> App::$args,
			'master'	=> Template::$master
		));

		new App($url);

		$request = array_shift(self::$requestStack);

		App::$controller = $request['args']['controller'];
		App::$controller = $request['args']['action'];
		App::$controller = $request['args']['module'];
		App::$args = App::$controller = $request['args'];
		Template::$master = $request['master'];

		return ob_get_clean();
	}
}