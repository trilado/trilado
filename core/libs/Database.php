<?php 
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe de persistência com o banco de dados. Implementa o padrão Singleton
 * 
 * @author		Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version		1
 *
 */
class Database 
{
	/**
	 * Guarda a instância da classe Database, pois utiliza o padrão Singleton
	 * @var	object
	 */
	protected static $instance;
	
	/**
	 * Guarda instâncias da classe DatabaseQuery
	 * @var	array
	 */
	protected $tables = array();
	
	/**
	 * Guarda as SQL das operações de inert, update e delete
	 * @var	array
	 */
	protected $operations = array();
	
	/**
	 * Construtor da classe, protegido para não criar um instância sem utilizar o Singleton
	 */
	protected function __construct()
	{
		
	}
	
	/**
	 * Método para instanciação do classe
	 * @return	object 	retorna a instância da classe Database
	 */
	public static function getInstance()
	{
		if(!self::$instance)
			self::$instance = new self();
		return self::$instance;
	}
	
	/**
	 * Chamado automáticamente quando uma propriedade de Database for chamada e ela não existir. Cria uma nova instância de DatabaseQuery 
	 * @param	string	$name	nome de uma tabela ou view do banco de dados
	 * @return	object			retorna uma instância de DatabaseQuery
	 */
	public function __get($name)
	{
		if($this->tables[$name])
			$this->operations = array_union($this->operations, $this->tables[$name]->getAndClearOperations());
		return $this->tables[$name] = new DatabaseQuery($name);
	}
	
	/**
	 * Submete para o banco de dados as operações realizadas nos models
	 * @throws	TriladoException	disparada quando ocorrer alguma exceção do tipo SQLException
	 * @throws	SQLException		disparada quando ocorrer alguma exceção no banco de dados
	 * @return	void
	 */
	public function save()
	{
		foreach($this->tables as $entity)
		{
			$this->operations = array_union($this->operations, $entity->getAndClearOperations());
			foreach($this->operations as $operation)
			{
				try
				{
					$stmt = DatabaseQuery::connection()->prepare($operation['sql']);
					$status = $stmt->execute($operation['values']);
					if(!$status)
					{
						$error = $stmt->errorInfo();
						throw new TriladoException($error[2]);
					}
					if($operation['model'])
						$key = $operation['model']->_setLastId($entity->lastInsertId());
				}
				catch(PDOException $ex)
				{
					throw new DatabaseException($ex->getMessage(), $ex->getCode());
				}
			}
			$this->operations = array();
		}
	}
	
	/**
	 * Inicioa uma transação
	 * @return	void
	 */
	public function transaction()
	{
		DatabaseQuery::connection()->beginTransaction();
	}
	
	/**
	 * Envia a transação
	 * @return	void
	 */
	public function commit()
	{
		DatabaseQuery::connection()->commit();
	}
	
	/**
	 * Cancela uma transação
	 * @return	void
	 */
	public function rollback()
	{
		DatabaseQuery::connection()->rollBack();
	}
}