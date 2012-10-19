<?php 
/*
 * Copyright (c) 2011-2012, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe de persistência com o banco de dados. Implementa os padrões Factory e Singleton
 * 
 * @author		Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version		2.2
 *
 */
class Database 
{
	/**
	 * Guarda as instâncias da classe Database, pois utiliza o padrão Singleton
	 * @var	object
	 */
	protected static $instances = array();
	
	/**
	 * Guarda instâncias da classe DatabaseQuery
	 * @var	array
	 */
	protected $tables = array();
	
	/**
	 * Guarda as configurações
	 * @var	array
	 */
	protected $config = array();


	/**
	 * Construtor da classe, protegido para não criar um instância sem utilizar o Singleton
	 */
	protected function __construct($config = null)
	{
		$this->config = $config;
	}
	
	/**
	 * Método para instanciação do classe
	 * @return	object 	retorna a instância da classe Database
	 */
	public static function getInstance()
	{
		$configs = Config::get('database');
		
		if(!isset($configs['default']))
			throw new ConfigNotFoundException('A configuração "database[default]" não foi encontrada');
		
		$configs['default']['connection'] = 'default';
		
		if(!isset(self::$instances['default']))
			self::$instances['default'] = new self($configs['default']);
		return self::$instances['default'];
	}
	
	public static function factory($config = 'default')
	{
		$configs = Config::get('database');
		
		if(!isset($configs[$config]))
			throw new ConfigNotFoundException('A configuração "database['. $config .']" não foi encontrada');
		
		$configs[$config]['connection'] = $config;
		
		if(!isset(self::$instances[$config]))
			self::$instances[$config] = new self($configs[$config]);
		return self::$instances[$config];
	}
	
	/**
	 * Chamado automáticamente quando uma propriedade de Database for chamada e ela não existir. Cria uma nova instância de Datasource 
	 * @param	string	$name	nome de uma tabela ou view do banco de dados
	 * @return	Datasource		retorna uma instância de Datasource
	 */
	public function __get($name)
	{
		return $this->tables[$name] = $this->datasource($name);
	}
	
	/**
	 * Submete para o banco de dados as operações realizadas nos models
	 * @return	void
	 */
	public function save()
	{
		foreach($this->tables as $entity)
			$entity->save();
	}
	
	private function datasource($entity = null)
	{
		$class = ucfirst(strtolower($this->config['type'])) . 'Datasource';
		Import::load('datasource', array($class));
		return new $class($this->config, $entity);
	}
	
	/**
	 * Executa uma instrução SQL sem a utilização de Model
	 * @param	string	$sql	comando de acordo com o banco informado
	 * @return	mixed			
	 */
	public function query($sql)
	{
		return $this->datasource()->query($sql);
	}
	
	/**
	 * Inicia uma transação
	 * @return	void
	 */
	public function transaction()
	{
		return $this->datasource()->transaction();
	}
	
	/**
	 * Envia a transação
	 * @return	void
	 */
	public function commit()
	{
		return $this->datasource()->commit();
	}
	
	/**
	 * Cancela uma transação
	 * @return	void
	 */
	public function rollback()
	{
		return $this->datasource()->rollback();
	}
}