<?php
/*
 * Copyright (c) 2012, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */


/**
 * Exceção disparada quando alguma configuração importante não foi encontrada, se não tratada pelo usuário resulta num erro 500
 * 
 * @author	Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version 1
 *
 */
class ConfigNotFoundException extends Exception
{	
	/**
	 * Contrutor da classe
	 * @param	string	$message	mensagem do erro
	 */
	public function __construct($message)
	{
		parent::__construct($message);
	}
}
