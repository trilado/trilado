<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção para controller que não herda da classe Controller, é trata pelo framework, que resulta no erro 500
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1
 *
 */
class ControllerInheritanceException extends TriladoException
{
	/**
	 * Controller da classe
	 * @param	string	$controller		nome do controller
	 */
	public function __construct($controller)
	{
		parent::__construct('A classe '. $controller .' não é subclasse de Controller');
	}
	/**
	 * (non-PHPdoc)
	 * @see	TriladoException::getDetails()
	 */
	public function getDetails()
	{
		return '&lt;?php'. nl .'class '. controller .' <b>extends Controller</b> {'. nl .'}';
	}
}