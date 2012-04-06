<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção para arquivo não encontrado, tratado pelo framework, que resulta num erro 500
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1
 *
 */
class FileNotFoundException extends TriladoException
{
	/**
	 * Construtor da classe
	 * @param	string	$file		endereço do arquivo
	 */
	public function __construct($file)
	{
		parent::__construct('O arquivo '. $file .' não foi encontrado');
	}
}