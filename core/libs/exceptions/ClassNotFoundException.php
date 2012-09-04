<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção para classe não encontrada, é tratada pela framework, que resulta numa página de erro interno
 *
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1
 *
 */
class ClassNotFoundException extends TriladoException
{
	/**
	 * Nome da classe que não foi encontrada
	 * @var	string
	 */
	protected $clazz;
	
	/**
	 * Contrutor da classe
	 * @param	string	$class		nome da classe não encontrada
	 */
	public function __construct($class)
	{
		$this->clazz = $class;
		parent::__construct('A classe '. $class .' não foi encontrada');
	}
	
	/**
	 * (non-PHPdoc)
	 * @see	TriladoException::getDetails()
	 */
	public function getDetails()
	{
		return '&lt;?php'. nl .'/**'. nl .' * @Entity()'. nl .' */'. nl .'class <b>'. $this->clazz .'</b> extends Model {'. nl . nl .'}';;
	}
}