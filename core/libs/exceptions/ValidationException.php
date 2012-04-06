<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção de validação de dados, deve ser tratada pelo programador para exibir a mensagem ao usuário
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1
 *
 */
class ValidationException extends TriladoException
{
	/**
	 * Construtor da classe
	 * @param	string	$message		mensagem do erro
	 * @param	int		$code			número do erro
	 */
	public function __construct($message, $code)
	{
		parent::__construct($message, $code);
	}
}