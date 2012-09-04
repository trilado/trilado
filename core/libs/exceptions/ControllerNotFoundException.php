<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Controller não encontrado, tratado pelo framework, que resulta numa página não encontrada
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1
 *
 */
class ControllerNotFoundException extends PageNotFoundException
{
	/**
	 * Contrutor da classe
	 * @param	string	$controller		nome do controller
	 */
	public function __construct($controller)
	{
		parent::__construct('O controller '. $controller .' não foi encontrado');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see	PageNotFoundException::getDetails()
	 */
	public function getDetails()
	{
		return '&lt;?php'. nl .'class <b>'. controller .'</b> extends Controller {'. nl . nl .'}';
	}
}