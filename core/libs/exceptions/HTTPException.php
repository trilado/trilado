<?php
/*
 * Copyright (c) 2013, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */


/**
 * Exceção padrão para o protocolo HTTP
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1
 *
 */
class HTTPException extends TriladoException
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
}