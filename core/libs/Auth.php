<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe para autenticação do usuário
 * 
 * @author		Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version		2
 *
 */ 
class Auth 
{
	/**
	 * Construtor da classe, é privado porque a classe só contém método estáticos e não pode instânciada
	 */
	private function __construct(){}
	
	/**
	 * Define um ou mais papéis para o usuário na sessão
	 * @param	string	$param1	nome do papél
	 * @param	string	$param2	nome do papél
	 * @param	string	$paramN	nome do papél
	 * @return	void
	 */
	public static function set()
	{
		Session::start();
		$roles = func_get_args();
		foreach($roles as $role)
			self::_set($role, $role);
	}
	
	/**
	 * Remove um mais papéis do usuário na sessão
	 * @param	string	$param1	nome do papél
	 * @param	string	$param2	nome do papél
	 * @param	string	$paramN	nome do papél
	 * @return	void
	 */
	public static function remove()
	{
		Session::start();
		$roles = func_get_args();
		foreach($roles as $role)
			self::_set($role, null);
	}
	
	/**
	 * Remove todos os papéis do usuário na sessão
	 * @return	void
	 */
	public static function clear()
	{
		Session::start();
		$_SESSION[self::key()] = null;
	}
	
	/**
	 * Verifica se o usuário possui, na sessão, os papéis informados no parâmetro
	 * @param	string	$param1	nome do papél
	 * @param	string	$param2	nome do papél
	 * @param	string	$paramN	nome do papél
	 * @throws	AuthException	dispara se o usuário estiver algum papél na sessão, porém este não for informado do parâmetro
	 * @return	void
	 */
	public static function allow()
	{
		Session::start();
		$roles = func_get_args();
		$is = call_user_func_array('Auth::is', $roles);
		if(!$is)
		{
			if(!self::isLogged())
			{
				$location = preg_match('@^~/@', default_login) ? root_virtual . trim(default_login, '~/') : default_login;
				header('Location: '. $location);
				exit;
			}
			throw new AuthException('Você não tem permissão para acessar esta página', 403);
		}
	}
	
	/**
	 * Verifica se o usuário possui um ou mais papéis informado como parâmetro
	 * @param	string	$param1	nome do papél
	 * @param	string	$param2	nome do papél
	 * @param	string	$paramN	nome do papél
	 * @return	boolean			retorna true se tiver um dos papéis, no contrário retorna false
	 */
	public static function is()
	{
		Session::start();
		$roles = func_get_args();
		foreach($roles as $role)
		{
			if(self::_get($role))
				return true;
		}
		return false;
	}
	
	/**
	 * Verifica se o usuário possuim um ou mais papéis na sessão
	 * @return	boolean		retorna true se o usuário possuir, caso contrário retorna false
	 */
	public static function isLogged()
	{
		Session::start();
		if(is_array($_SESSION[self::key()]))
		{
			foreach($_SESSION[self::key()] as $role)
			{
				if($role)
					return true;
			}
		}
		return false;
	}
	
	/**
	 * Pega um papél na sessão
	 * @param	string	$key	nome do papél
	 * @return	string			retorna o papél
	 */
	private static function _get($key)
	{
		return $_SESSION[self::key()][$key];
	}
	
	/**
	 * Adiciona um papél na sessão
	 * @param	string	$key	nome do papél
	 * @param	string	$value	valor
	 * @return	void
	 */
	private static function _set($key, $value)
	{
		$_SESSION[self::key()][$key] = $value;
	}
	
	/**
	 * Gera uma chave MD5 com base no navegador do usuário e no salt, definido na configuração
	 * @return	string	retorn o MD5 gerado
	 */
	private static function key()
	{
		return 'Auth.'. md5($_SERVER['HTTP_USER_AGENT'] . salt);
	}
}