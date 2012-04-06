<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção para diretório não encontrado, tratado pelo framework, resulta num erro 500
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1
 *
 */
class DirectoryNotFoundException extends TriladoException
{
	/**
	 * Contrutor da classe
	 * @param	string	$directory	endereço do diretório
	 */
	public function __construct($directory)
	{
		parent::__construct('O diretório '. $directory .' não foi encontrado');
	}
}