<?php
/*
 * Copyright (c) 2011-2013, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção para usuário não autenticado, é tratada pelo framework, que resulta numa página 403 ou redireciona para página de login
 * dependendo da sessão do usuário
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1.1
 *
 */
class UnauthorizedException extends HTTPException
{
	/**
	 * Construtor da classe
	 * @param	string	$message	mensagem a ser exibida ao usuário
	 * @param	int		$code		código do erro
	 */
	public function __construct($message, $code)
	{
		parent::__construct($message, 401);
	}
}