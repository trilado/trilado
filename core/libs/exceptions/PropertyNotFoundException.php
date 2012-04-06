<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Exceção para propriedade não encontrada, tratada pelo framework, que resulta num erro 500
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666gmail.com>
 * @version	1
 *
 */
class PropertyNotFoundException extends TriladoException
{
	/**
	 * Construtor da classe
	 * @param	string	$property	nome da propriedade
	 */
	public function __construct($property)
	{
		parent::__construct('A propriedade '. $property .' não foi encontrada');
	}
}