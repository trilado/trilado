<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção para tipo de retorno inválido, utilizada no retorno da view, tratada pelo framework, que resulta num erro 500
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1
 *
 */
class InvalidReturnException extends TriladoException
{
	/**
	 * Contrutor da classe
	 * @param	string	$action		nome action
	 */
	public function __construct($action)
	{
		parent::__construct('A action '. $action .' deve retornar algo');
	}
	/**
	 * (non-PHPdoc)
	 * @see	TriladoException::getDetails()
	 */
	public function getDetails()
	{
		return '&lt;?php'. nl .'class '. controller .' extends Controller {'. 
		nl . nl . t() .'public function '. action .'() {'. nl . t(2) . '<b>return $this->_view();</b>' . nl . t() .'}' . nl .'}';
	}
}