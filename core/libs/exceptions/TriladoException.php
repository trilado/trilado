<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção padrão do framework, é herda por várias outras classes de exceção
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1
 *
 */
class TriladoException extends Exception
{
	/**
	 * Construtor da classe
	 * @param	string	$msg		mensagem do erro
	 * @param	int		$code		código do erro
	 */
	public function __construct($msg, $code = 500)
	{
		parent::__construct($msg, $code);
	}
	
	/**
	 * Se o debug estiver habilitado, informa ao usuário detalhes sobre o erro
	 */
	public function getDetails()
	{
		return '';
	}
}