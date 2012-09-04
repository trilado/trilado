<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção para action é definida como estática, é tratada pela framework, que resulta numa página não encontrada
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1
 *
 */
class ActionStaticException extends PageNotFoundException
{
	/**
	 * Contrutor da classe
	 * @param	string	$action	nome da action
	 */
	public function __construct($action)
	{
		parent::__construct('A action '. $action .' não pode ser estática');
	}
	
	/**
	 * Se o debug estiver habilitado, informa ao usuário detalhes sobre a action
	 * @see		PageNotFoundException::getDetails()
	 * @return	string		retorna os detalhes da action
	 */
	public function getDetails()
	{
		return '&lt;?php'. nl .'class '. controller .' extends Controller {'. 
		nl . nl . t() .'public <b>static</b> function '. action .'() {'. nl . t(2) . 'return $this->_view();' . nl . t() .'}' . nl .'}';
	}
}