<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção para erro de entrada ou saída
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version 1
 *
 */
class IOException extends TriladoException
{
	/**
	 * Construtor da classe
	 * @param	string	$msg	mensagem do erro
	 */
	function __construct($msg)
	{
		parent::__construct($msg);
	}
}