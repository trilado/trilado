<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção quando ocorre algum erro no banco de dados (utilizando as classes Database e DatabaseQuery), se não tratada pelo usuário resulta num erro 500
 * 
 * @author	Valdirene da Cruz Neves Júnior
 * @version 1
 *
 */
class DatabaseException extends Exception
{
	/**
	 * Linha do erro
	 * @var	int
	 */
	private $codeLine;
	
	/**
	 * Contrutor da classe
	 * @param	string	$message	mensagem do erro
	 */
	public function __construct($message)
	{
		parent::__construct($message);
	}
}
