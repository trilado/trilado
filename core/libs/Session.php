<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com> 
 * All rights reserved.
 */


/**
 * Classe para manipulação de Sessões
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	2
 *
 */
class Session
{
	/**
	 * Contrutor da classe, é privado para não criar uma instância
	 */
	private function __construct()
	{
	}
	
	/**
	 * Inicia a sessão
	 * @return void
	 */
	public static function start()
	{
		if(defined('session_started'))
			return true;
		define('session_started', true);
		session_start();
		session_regenerate_id();
	}
	
	/**
	 * Cria uma chave MD5 com base no navegador do usuário e o salt, definido na configuração
	 * @return string	retorna uma string MD5 
	 */
	private static function key()
	{
		return 'Trilado.'. md5($_SERVER['HTTP_USER_AGENT'] . salt);
	}
	
	/**
	 * Cria uma sessão criptograda para o usuário
	 * @param string $name		nome da sessão
	 * @param mixed $value		valor da sessão
	 * @throws TriladoException	disparada caso o programador não defina a configuração 'salt', ou o valor esteja vazio
	 * @return void
	 */
	public static function set($name , $value)
	{
		if(!defined('salt') || salt == '')
			throw new TriladoException("A configuração 'salt' não pode ter o valor nulo");
		self::start();
		$_SESSION[self::key()][$name] = Security::encrypt($value, salt);
	}
	
	/**
	 * Remove uma sessão do usuário
	 * @param string $name	nome da sessão a ser removida
	 * @return void
	 */
	public static function del($name)
	{
		self::start();
		$_SESSION[self::key()][$name] = null;
	}
	
	/**
	 * Remove todas as sessões do usuário
	 * @return void
	 */
	public static function clear()
	{
		self::start();
		$_SESSION[self::key()] = null;
	}
	
	/**
	 * Descriptograda e retorna uma sessão específica do usuário
	 * @param string $name		nome da sessão a ser retornada
	 * @throws TriladoException	disparado se a configuração 'salt' não for definida ou o valor for vazio
	 * @return mixed			retorna o valor sessão descriptografado
	 */
	public static function get($name)
	{
		if(!defined('salt') || salt == '')
			throw new TriladoException("A configuração 'salt' não pode ter o valor nulo");
		self::start();
		return Security::decrypt($_SESSION[self::key()][$name], salt);
	}
}
