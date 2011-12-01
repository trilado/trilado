<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe Model representa uma entidade do banco de dados, deve ser herdada, nela deve ficar a lógica de negócio da aplicação. Já vem com  métodos para as operações CRUD prontas
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	2
 *
 */
class Model
{
	/**
	 * Guarda true se a classe for uma nova instância de Model e false a instância vinher do banco
	 * @var boolean
	 */
	private $_isNew = true;
	
	/**
	 * Verifica se a classe é uma nova instância de Model ou se os valores vem do banco
	 * @return boolean	retorna true se classe foi instânciada pelo usuário, ou false se foi instânciada pela classe DatabaseQuery
	 */
	public function _isNew()
	{
		return $this->_isNew;
	}
	
	/**
	 * Define se a classe é ou não uma nova instância. Esse método não deve ser chamado
	 * @return voi
	 */
	public function _setNew()
	{
		$this->_isNew = false;
	}
}
