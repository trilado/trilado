<?php
/*
 * Copyright (c) 2012, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe de Mapemamento de Objeto Relacional (ORM), que é utilizada em conjunto com a classe Database para manipular o banco de dados
 * utilizando orientação a objetos de acordo com o MySQL.
 * 
 * @author	Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version	0.6
 *
 */
class MysqlDatasource extends Datasource
{
	/**
	 * Guarda a lista de conexões com os bancos de dados
	 * @var	array
	 */
	protected static $connections = null;
	
	/**
	 * Guarda as instruções geradas para inserção, atualização e deleção de dados das tabelas
	 * @var	array
	 */
	protected $operations = array();
	
	/**
	 * Guarda uma instância da classe Annotation referente ao model que está sendo trabalhado
	 * @var	object
	 */
	protected $annotation;
	
	/**
	 * Guarda as propriedades do model que está sendo trabalhado
	 * @var	array
	 */
	protected $properties = array();
	
	/**
	 * Guarda o nome da classe do model
	 * @var	string
	 */
	protected $clazz;
	
	/**
	 * Guarda o nome da tabela que o model representa
	 * @var	string
	 */
	protected $table;
	
	/**
	 * Guarda o nome dos atributos que serão retornas da tabela na hora da execução das instrução SQL
	 * @var	string
	 */
	protected $select;
	
	/**
	 * Guarda os condicionais do instrução SQL
	 * @var	string
	 */
	protected $where = '';
	
	/**
	 * Guarda os valores dos condicionais
	 * @var	array
	 */
	protected $where_params = array();
	
	/**
	 * Guarda a condição de ordenação
	 * @var	string
	 */
	protected $orderby = '';
	
	/**
	 * Guarda o limite máximo de resultados que poderão ser retornados
	 * @var	int
	 */
	protected $limit = '';
	
	/**
	 * Guarda a posição em que começarão os resultados
	 * @var	int
	 */
	protected $offset = '';
	
	/**
	 * Guarda a informação se vai utilizar ou não distinção dos resultados
	 * @var	string
	 */
	protected $distinct = '';
	
	/**
	 * Guarda a cláusula GROUP BY
	 * @var	string
	 */
	protected $groupBy = '';


	/**
	 * Indica se o resultado a instrução é uma operão soma, média, valor mínimo e etc.
	 * @var	boolean
	 */
	protected $calc = false;
	
	/**
	 * Guarda as configurações de conexão
	 * @var	array 
	 */
	protected $config = array();

	/**
	 * Guarda o tempo do cache
	 * @var	int
	 */
	protected $cache = -1;

	/**
	 * Construtor da classe
	 * @param	array	$config		configurações de conexão com o banco de dados
	 * @param	string	$class		nome do model
	 * @throws	DatabaseException	dispara se o model não tiver a anotação de Entity ou View
	 */
	public function __construct($config, $class = null)
	{
		$this->config = $config;
		if($class)
			$this->from($class);
	}
	
	/**
	 * Define com qual entidade será trabalhado
	 * @param	string	$class		nome da entidade
	 * @return	MysqlDatasource		retorna a própria instância da classe MysqlDatasource 
	 * @throws	DatabaseException	dispara se o model não tiver a anotação de Entity ou View
	 */
	public function from($class)
	{
		$this->clazz = $class;
		$this->annotation = Annotation::get($class);
		$this->properties = $this->annotation->getProperties();
		
		$annotation_class = $this->annotation->getClass();
		if(!property_exists($annotation_class, 'Entity') && !property_exists($annotation_class, 'View'))
			throw new DatabaseException("A classe '". $class ."' não é uma entidade ou view");
			
		$this->table = isset($annotation_class->Entity) ? $annotation_class->Entity : (isset($annotation_class->View) ? $annotation_class->View : $class);
		
		return $this;
	}
	
	/**
	 * Método estático que faz a conexão com o banco de dados
	 * @throws	DatabaseException	dispara se ocorrer algum exceção do tipo PDOException
	 * @return	object				retorna uma instância da classe PDO que representa a conexão
	 */
	public function connection()
	{
		if(isset(self::$connections[$this->config['connection']])) 
			return self::$connections[$this->config['connection']];
		try
		{
			self::$connections[$this->config['connection']] = new PDO('mysql:dbname='. $this->config['name'] .';host='. $this->config['host'], $this->config['user'], $this->config['pass']);
			self::$connections[$this->config['connection']]->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES ' . str_replace('-', '', Config::get('charset')));
			self::$connections[$this->config['connection']]->setAttribute(PDO::ATTR_PERSISTENT, true);
			return self::$connections[$this->config['connection']];
		}
		catch(PDOException $e)
		{
			throw new DatabaseException($e->getMessage());
		}
	}
	
	/**
	 * Pega as instrucões SQL geradas e limpa a propriedade $operations
	 * @return	array	retorna um array com as SQLs
	 */
	public function getAndClearOperations()
	{
		$operations = $this->operations;
		$this->operations = array();
		return $operations;
	}
	
	/**
	 * Adiciona as condições na instrunção (clausula WHERE)
	 * @param	string	$condition		condições SQL, por exemplo 'Id = ? OR slug = ?'
	 * @param	mixed	$value1			valor da primeira condição
	 * @param	mixed	$valueN			valor da x condição
	 * @throws	DatabaseException		disparado se a quantidade de argumentos for menor 2 ou se quantidade de condicionais não corresponder a quantidade de valores
	 * @return	object					retorna a própria instância da classe MysqlDatasource 
	 */
	public function where()
	{
		if(func_num_args() < 2)
			throw new DatabaseException('O método where() deve conter no mínimo 2 parâmetros');
		
		$args = func_get_args();
		$where = $args[0];
		array_shift($args);
		$params = $args;
		
		if(substr_count($where, '?') !== count($params))
			throw new DatabaseException('Quantidade de parâmetros está diferente');
			
		$this->where = $where;
		$this->where_params = $params;
		return $this;
	}
	
	/**
	 * Adiciona as condições na instrução (clausula WHERE)
	 * @param	string	$where		condições SQL, por exemplo 'Id = ? OR slug = ?'
	 * @param	array	$params		array com os valores das condições
	 * @return	object				retorna a própria instância da classe MysqlDatasource
	 */
	public function whereArray($where, $params)
	{
		if(substr_count($where, '?') !== count($params))
			throw new DatabaseException('Quantidade de parâmetros está diferente');
		
		$this->where = $where;
		$this->where_params = $params;
		return $this;
	}
	
	/**
	 * Adiciona as condições na instrução SQL (clausula WHERE)
	 * @param	string	$where		condições SQL com valores direto, por exemplo 'Description IS NOT NULL'
	 * @return	object				retorna a própria instância da classe MysqlDatasource
	 */
	public function whereSQL($where)
	{
		$this->where = $where;
		return $this;
	}
	
	/**
	 * Define a ordem em que os resultados serão retornados
	 * @param	string	$order	nome da coluna a ser ordenada
	 * @param	string	$type	typo de ordenação (asc ou desc)
	 * @return	object			retorna a própria instância da classe MysqlDatasource
	 */
	public function orderBy($order, $type = null)
	{
		if (is_array($order))
		{
			if(!is_array($type))
				throw new DatabaseException('Ambos os parâmetro devem ser do mesmo tipo');
			elseif(count($type) !== count($order))
				throw new DatabaseException('Quantidade de parâmetros está diferente');
			
			$o = array();
					
			foreach ($order as $k => $v)
			{
				$o[] = $v . ' ' . $type[$k];
			}
			
			$this->orderby = implode(', ', $o);
		}
		else
		{
			$this->orderby = $order . ($type ? ' ' . $type : '');
		}

		return $this;
	}
	
	/**
	 * Define como ordem decrescente os resultados que serão retornados
	 * @param	string	$order	nome da coluna a ser ordenada
	 * @return	object			retorna a própria instância da classe MysqlDatasource
	 */
	public function orderByDesc($order)
	{
		if (is_array($order))
		{
			foreach ($order as $k => $v)
			{
				$order[$k] = $v . ' DESC';
			}
			
			$this->orderby = implode(', ', $order);
		}
		else
		{
			$this->orderby = $order . ' DESC';
		}
		
		return $this;
	}
	
	/**
	 * Define um limite máximo de itens a serem retornados
	 * @param	int	$n	valor do limite
	 * @param	int	$o	valor do offset
	 * @return	object	retorna a própria instância da classe MysqlDatasource
	 */
	public function limit($n, $o = null)
	{
		$this->limit = $n;
		if($o) 
			$this->offset = $o;
		return $this;
	}
	
	/**
	 * Define a posição em que os resultados iniciam
	 * @param	int	$n	valor da posição
	 * @return	object	retorna a própria instância da classe MysqlDatasource
	 */
	public function offset($n)
	{
		$this->offset = $n;
		return $this;
	}
	
	/**
	 * Define que os resultados serão distintos
	 * @return	object	retorna a própria instância da classe MysqlDatasource
	 */
	public function distinct()
	{
		$this->distinct = 'DISTINCT ';
		return $this;
	}
	
	/**
	 * Agrupa por colunas (cláusula GROUP BY)
	 * @param	mixed	$value1			nome de uma coluna
	 * @param	mixed	$valueN			nome da x coluna
	 * @throws	DatabaseException		disparado se a quantidade de argumentos for menor 1
	 * @return	object					retorna a própria instância da classe MysqlDatasource 
	 */
	public function groupBy()
	{
		if(func_num_args() < 1)
			throw new DatabaseException('O método groupBy() deve conter no mínimo 1 parâmetro');
		
		$params = func_get_args();
			
		$this->groupBy = $params;
		return $this;
	}
	
	/**
	 * Gerar e retorna o SQL da consulta
	 * @return	string	retorna o SQL gerado
	 */
	public function getSQL($values = true)
	{
		$select = $this->select;
		if(!$select)
			$select = $this->table .'.*';
		
		$joins = '';
		//joins
		
		$where = $this->where ? ' WHERE '. $this->where : '';
		$orderby = $this->orderby ? ' ORDER BY '. $this->orderby : '';
		$limit = $this->limit ? ' LIMIT '. $this->limit : '';
		$offset = $this->offset ? ' OFFSET '. $this->offset : '';
		$groupby = $this->groupBy ? ' GROUP BY `'. implode('`, `', $this->groupBy) .'`' : '';
		
		$sql = 'SELECT '. $this->distinct . $select .' FROM '. $this->table . $joins . $where . $groupby . $orderby . $limit . $offset;
		if($values)
			$sql = $this->bindValues ($sql, $this->where_params);
		
		return $sql;
	}
	
	/**
	 * Monta a instrunção SQL a partir da operações chamadas e executa a instrução
	 * @throws	DatabaseException	disparada caso ocorra algum erro na execução da operação
	 * @return	array				retorna um array com instâncias do Model
	 */
	public function all()
	{
		if (func_num_args() > 0) 
		{
			$reflectionMethod = new ReflectionMethod('MysqlDatasource', 'where');
			$args = func_get_args();
			$reflectionMethod->invokeArgs($this, $args);
		}
		
		$sql = $this->getSQL(false);

		Debug::add($this->getSQL());
		
		$key = md5($this->getSQL());
		
		if(Cache::enabled())
		{
			$cache = Cache::factory();
			if($cache->has($key))
				return $cache->read($key);
		}
		
		$stmt = $this->connection()->prepare($sql);
		$status = $stmt->execute($this->where_params);
		if(!$status)
		{
			$error = $stmt->errorInfo();
			throw new DatabaseException($error[2]);
		}
		
		$results = array();
		if($stmt->rowCount() > 0)
		{
			
			$annotation = Annotation::get($this->clazz);
			$i = 0;
			while($result = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				if($this->calc)
				{
					if(Cache::enabled())
					{
						$config = Config::get('cache');
						$cache = Cache::factory();
						$cache->addToGroup($this->clazz, $key);
						$cache->write($key, $result['calc'], $config['time'] * MINUTE);
					}
					return $result['calc'];
				}
				$model = $this->clazz;
				$object = new $model();	
				$object->_setNew();	
				
				foreach($result as $field => $value)
				{
					if($this->config['validate'] || property_exists($object, $field))
					{
						$property = $annotation->getProperty($field);
						$type = strtolower($property->Column->Type);

						$types = array('boolean','integer','float','string','array','object','null');

						if($type === 'double')
							$type = 'float';
						elseif($type === 'int')
							$type = 'integer';
						elseif($type === 'boolean')
							$value = $value == 1;
						elseif($type === 'datetime')
							$type = 'string';

						if(!in_array($type, $types))
							throw new DatabaseException('O tipo de dados '. $type .' é inválido');

						if($type !== 'datetime')
							settype($value, $type);
						$object->{$field} = $value;
					}
				}
				
				$results[$i] = $object;
				++$i;
			}
		}
		else
		{
			if($this->calc)
				$results = 0;
		}
		if(Cache::enabled())
		{
			$config = Config::get('cache');
			$cache = Cache::factory();
			$cache->addToGroup($this->clazz, $key);
			$cache->write($key, $results, $config['time'] * MINUTE);
		}
		
		return $results;
	}
	
	/**
	 * Monta a instrução SQL a partir das operações chamadas e executa a instrução
	 * @return	object	retorna uma instância do Model com os valores preenchidos de acordo com o banco
	 */
	public function single()
	{
		$this->limit(1);
		
		$reflectionMethod = new ReflectionMethod('MysqlDatasource', 'all');
		$args = func_get_args();
		$result = $reflectionMethod->invokeArgs($this, $args);
		
		if(count($result)) 
			return $result[0];
	}
	
	/**
	 * Monta a instrução SQL com paginação de resultado, executa a instrução
	 * @param	int	$p		o número da página que quer listar os resultados (começa com zero)
	 * @param	int	$m		quantidade máxima de itens por página
	 * @return	object		retorna um objeto com as propriedade Data (contendo um array com os resultados) e Count (contento a quantidade total de resultados)
	 */
	public function paginate($p, $m)
	{
		$p = ($p < 0 ? 0 : $p) * $m;
		$result = new stdClass;
		$this->limit = $m;
		$this->offset = $p;
		$result->Data = $this->all();
		$this->limit = $this->offset = null;
	
		$result->Count = $this->count();
		return $result;
	}
	
	/**
	 * Monta a instrução SQL a partir das operações chamadas e executa a instrução
	 * @param	string	$operation		operação a ser executada, tipo SUM, AVG, MIN e etc
	 * @param	string	$column			colunas da tabela em que a operação se aplica
	 * @return 	int						retorna o valor da operação 
	 */
	protected function calc($operation, $column)
	{
		$this->calc = true;
		$this->select = $operation .'('. $column .') AS calc';
		return $this->all();
	}
	
	/**
	 * Calcula quantos resultados existem na tabela aplicando as regras dos métodos chamados anteriormente
	 * @param	string	$column		coluna a ser verifica a quantidade
	 * @return	int		retorna a quantidade
	 */
	public function count($column = null)
	{
		if(!$column)
			$column = '*';
		return $this->calc('COUNT', $column);
	}
	
	/**
	 * Calcula a soma de todos os valores da coluna expecificada
	 * @param	string	$column		coluna a ser somada
	 * @return	double				retorna a soma dos valores de cada linha
	 */
	public function sum($column)
	{
		return $this->calc('SUM', $column);
	}
	
	/**
	 * Calcula o maior valor de uma coluna expecifica
	 * @param	string	$column		nome da coluna a ser calculada
	 * @return	double				retorna o maior valor	
	 */
	public function max($column)
	{
		return $this->calc('MAX', $column);
	}
	
	/**
	 * Calcula o menor valor de uma coluna expecifica
	 * @param	string	$column		nome da coluna a ser calculada
	 * @return	double				retorna o menor valor
	 */
	public function min($column)
	{
		return $this->calc('MIN', $column);
	}
	
	/**
	 * Calcula a média de uma coluna expecifica, somando todos os valores dessa coluna e divindo pela quantidade de linhas existentes
	 * @param	string	$column		nome da coluna a ser calculada
	 * @return	double				retorna a média calculada
	 */
	public function avg($column)
	{
		return $this->calc('AVG', $column);
	}
	
	/**
	 * Verifica se o model possui algum relacionamento com outro model
	 * @return	array	retorna null caso não possua, mas se possuir retorna os relacionamentos
	 */
	protected function isRelated()
	{
		$relationships = array();
		foreach($this->properties as $name => $property)
		{
			if($this->isField($property) && $property->Foreign)
				$relationships[$name] = $property->Foreign;
		}
		return count($relationships) ? $relationships : null;
	}
	
	/**
	 * Verifica se uma propriedade expecifica do model representa uma coluna na tabela
	 * @param	object	$property	annotation da propriedade
	 * @return	boolean				retorna true se for uma coluna ou false caso contrario
	 */
	protected function isField($property)
	{
		return count((array)$property) > 0;
	}
	
	/**
	 * Valida uma propriedade expecifica do model
	 * @param	object	$property			anotação da propriedade
	 * @param	string	$field				nome da propriedade	
	 * @param	mixed	$value				valor da propriedade
	 * @throws	ValidationException			disparada caso o valor seja inválido
	 * @return	void
	 */
	protected function validate($property, $field, $value)
	{
		$label = isset($property->Label) ? $property->Label : $field;
		$functions = array('Int' => 'is_int', 'String' => 'is_string', 'Double' => 'is_double', 'Boolean' => 'is_bool');
		$is_type = $functions[$property->Column->Type];
		
		$value = $this->defaultValue($property->Column->Type, $value);
		
		if ($value == null && isset($property->AutoGenerated))
			return true;
		if(is_object($value))
			throw new ValidationException("O valor de '{$label}' não pode ser um objeto", 90400);
		if(isset($property->Required) && ($value === '' || $value === null)) 
			throw new ValidationException("O campo '{$label}' é obrigatório", 90401);
		if($is_type && !$is_type($value))
			throw new ValidationException("O campo '{$label}' só aceita valor do tipo '{$property->Column->Type}'", 90402);
		if(isset($property->Regex) && !preg_match('#'. $property->Regex->Pattern .'#', $value))
			throw new ValidationException($property->Regex->Message, 90403);
	}
	
	/**
	 * Normaliza um valor de acordo com o padrão do seu tipo
	 * @param	string	$type		tipo da propriedade
	 * @param	mixed	$value		valor da propriedade
	 * @return	mixed				retorna o valor normalizado caso seja null ou o próprio valor se não for null
	 */
	protected function defaultValue($type, $value)
	{
		if($type === 'String' && $value === null)
			$value = '';
		elseif($type === 'Boolean' && $value === null)
			$value = false;
		elseif(($type === 'Int' && $value === null) || ($type === 'Double' && $value === null))
			$value = 0;
		return $value;
	}
	
	/**
	 * Verifica se o tipo da propriedade é string
	 * @param	object	$property	anotação da propriedade
	 * @return	boolean				retorna true se for do tipo string, no contrário retorna false
	 */
	protected function isString($property)
	{
		$types = array('String','Date','DateTime');
		return in_array($property->Column->Type, $types);
	}
	
	/**
	 * Verifica se propriedade é chave primária
	 * @param	object	$property	anotação da propriedade
	 * @return	boolean				retorna true se for chave primária, no contrário retorna false
	 */
	protected function isKey($property)
	{
		return isset($property->Column) && isset($property->Column->Key);
	}
	
	/**
	 * Cria uma instrução SQL de inserção no banco
	 * @param	Model	$model		model a ser inserido
	 * @throws	DatabaseException	disparada caso o model não seja uma nova instância, ou não tenha a anotação Entity
	 * @return	void
	 */
	public function insert(Model $model)
	{
		$fields = array();
		$values = array();
		
		if(get_class($model) != $this->clazz)
			throw new DatabaseException("O objeto deve ser do tipo '". $this->clazz ."'");
		
		$class = $this->annotation->getClass();
		if(!$class->Entity)
			throw new DatabaseException('A classe '. get_class($model) .' não é uma entidade');
		
		if(!$model->_isNew()) 
			throw new DatabaseException('Para usar o método inserir é preciso criar uma nova instância de '. $this->clazz);
		
		foreach($model as $field => $value)
		{	
			$property = $this->annotation->getProperty($field);
			if($this->isField($property) && !isset($property->AutoGenerated))
			{
				if($this->config['validate'])
					$this->validate($property, $field, $value);
				
				if ($value === null) 
					continue;
				
				if(isset($property->Column) && isset($property->Column->Name))
					$field = $property->Column->Name;
				
				if (is_bool($value))
					$value = $value ? '1' : '0';
				
				$fields[] = '`'. $field .'`';
				$values[] = $value;
			}
		}
		$entity = $class->Entity ? $class->Entity : get_class($model);
			
		$sql = 'INSERT INTO `'. $entity .'` ('. implode($fields, ', ') .') VALUES ('. implode(',', array_fill(0, count($values), '?')) .');';
		
		Debug::add($this->bindValues($sql, $values));
		$this->operations[] = array('sql' => $sql, 'values' => $values, 'model' => $model);
	}
	
	/**
	 * Cria uma instrução SQL de atualização no banco
	 * @param	Model	$model		model a ser atualizado
	 * @throws	DatabaseException	disparada caso o model seja uma nova instância, ou não tenha a anotação Entity
	 * @return	void
	 */
	public function update(Model $model)
	{
		$fields = array();
		$values = array();
		$conditions = array();
		
		if(get_class($model) != $this->clazz)
			throw new DatabaseException("O objeto deve ser do tipo '". $this->clazz ."'");
		
		$class = $this->annotation->getClass();
		if(!$class->Entity)
			throw new DatabaseException('A classe '. get_class($model) .' não é uma entidade');
		
		if($model->_isNew()) 
			throw new DatabaseException('O método update não pode ser utilizado com uma nova instância de '. $this->clazz);
		
		foreach($model as $field => $value)
		{	
			$property = $this->annotation->getProperty($field);
			if($this->isField($property))
			{
				if($this->isKey($property))
				{
					$conditions['fields'][] = '`'. $field .'` = ?';
					$conditions['values'][] = $value;
				}
				else
				{
					if($this->config['validate'])
						$this->validate($property, $field, $value);
					
					if ($value === null) 
						continue;
					
					if(isset($property->Column) && isset($property->Column->Name))
						$field = $property->Column->Name;
					
					if (is_bool($value))
						$value = $value ? '1' : '0';
					
					$fields[] = '`'. $field .'` = ?';
					$values[] = $value;
				}
			}
		}
		$entity = $class->Entity ? $class->Entity : get_class($model);
		$sql = 'UPDATE `'. $entity .'` SET '. implode(', ', $fields) .' WHERE '. implode(' AND ', $conditions['fields']) .';';
		
		Debug::add($this->bindValues($sql, array_merge($values, $conditions['values'])));
		$this->operations[] = array('sql' => $sql, 'values' => array_merge($values, $conditions['values']));
	}
	
	/**
	 * Cria uma instrução SQL de deleção no banco
	 * @param	Model	$model		model a ser deletado
	 * @throws	DatabaseException	disparada caso o model seja uma nova instância, ou não tenha a anotação Entity
	 * @return	void
	 */
	public function delete(Model $model)
	{
		$conditions = array();
		
		if(get_class($model) != $this->clazz)
			throw new DatabaseException("O objeto deve ser do tipo '". $this->clazz ."'");
		
		$class = $this->annotation->getClass();
		if(!$class->Entity)
			throw new DatabaseException('A classe '. get_class($model) .' não é uma entidade');
		
		if($model->_isNew()) 
			throw new DatabaseException('O método delete não pode ser utilizado com uma nova instância de '. $this->clazz);
		
		foreach($model as $field => $value)
		{	
			$property = $this->annotation->getProperty($field);
			if($this->isField($property) && $this->isKey($property))
			{
				$conditions['fields'][] = '`'. $field .'` = ?';
				$conditions['values'][] = $value;
			}
		}
		$entity = $class->Entity ? $class->Entity : get_class($model);
		$sql = 'DELETE FROM `'. $entity .'` WHERE '. implode(' AND ', $conditions['fields']) .';';
		
		Debug::add($this->bindValues($sql, $conditions['values']));
		
		$this->operations[] = array('sql' => $sql, 'values' => $conditions['values']);
	}
	
	/**
	 * Cria uma instrução SQL de deleção no banco
	 * @return	void
	 */
	public function deleteAll()
	{
		if (func_num_args() > 0) 
		{
			$reflectionMethod = new ReflectionMethod('MysqlDatasource', 'where');
			$args = func_get_args();
			$reflectionMethod->invokeArgs($this, $args);
		}
		
		$where = $this->where ? ' WHERE '. $this->where : '';
		$orderby = $this->orderby ? ' ORDER BY '. $this->orderby : '';
		$limit = $this->limit ? ' LIMIT '. $this->limit : '';
		
		$sql = 'DELETE FROM '. $this->table . $where . $orderby . $limit .';';
		
		Debug::add($this->bindValues($sql, $this->where_params));
		$this->operations[] = array('sql' => $sql, 'values' => $this->where_params);
	}
	
	/**
	 * Pega o ID da ultima instrunção de um model específico
	 * @return	int		retorna o valor do ID
	 */
	public function lastInsertId()
	{
		return $this->connection()->lastInsertId($this->table);
	}
	
	/**
	 * Submete para o banco de dados as operações realizadas no model
	 * @throws	DatabaseException	disparada quando ocorrer alguma exceção no banco de dados
	 * @return	void
	 */
	public function save()
	{
		foreach($this->operations as $operation)
		{
			try
			{
				$stmt = $this->connection()->prepare($operation['sql']);
				$status = $stmt->execute($operation['values']);
				if(!$status)
				{
					$error = $stmt->errorInfo();
					throw new DatabaseException($error[2]);
				}
				if(isset($operation['model']))
					$operation['model']->_setLastId($this->lastInsertId());
			}
			catch(PDOException $ex)
			{
				throw new DatabaseException($ex->getMessage(), $ex->getCode());
			}
		}
		if(Cache::enabled())
		{
			$cache = Cache::factory();
			$cache->deleteGroup($this->clazz);
		}
		$this->operations = array();
	}
	
	/**
	 * Executa uma instrução SQL sem a utilização de Model
	 * @param	string	$sql		comando de acordo com o banco informado
	 * @throws	DatabaseException	disparada quando ocorrer alguma exceção no banco de dados
	 * @return	mixed			
	 */
	public function query($sql)
	{
			$stmt = $this->connection()->prepare($sql);
			$status = $stmt->execute();
			if(!$status)
			{
				$error = $stmt->errorInfo();
				throw new DatabaseException($error[2]);
			}
			if($stmt->rowCount() > 0)
			{
				$results = array();
				while($result = $stmt->fetch(PDO::FETCH_ASSOC))
				{
					$object = new stdClass();	
					foreach($result as $field => $value)
						$object->{$field} = $value;
					$results[] = $object;
				}
				return $results;
			}
			return array();
	}
	
	/**
	 * Inicia uma transação
	 * @return	void
	 */
	public function transaction()
	{
		$this->connection()->beginTransaction();
	}
	
	/**
	 * Envia a transação
	 * @return	void
	 */
	public function commit()
	{
		$this->connection()->commit();
	}
	
	/**
	 * Cancela uma transação
	 * @return	void
	 */
	public function rollback()
	{
		$this->connection()->rollBack();
	}
	
	/**
	 * Define que a consulta será realizada primeiro em cache
	 * @return	Datasource	retorna a própria instância da classe Datasource
	 */
	public function cache($time = 10)
	{
		$this->cache = $time;
		return $this;
	}
	
	/**
	 * Substitui as "?" (interrogações) pelos valores
	 * @param	string	$sql		instrução SQL
	 * @param	array	$values		valores da instruções SQL
	 * @return	string				retorna a instrução com os valores substituidos
	 */
	private function bindValues($sql, $values)
	{
		$parts = explode('?', trim($sql));
		$sql = '';
		for($i = 0; $i < count($parts); $i++)
		{
			if(isset($parts[$i]))
				$sql .= $parts[$i];
			if(isset($values[$i]))
				$sql .= $this->sanitize($values[$i]);
		}
		return $sql;
	}
	
	/**
	 * Normaliza um valor de acordo com o SQL
	 * @param	mixed	$value	o valor a ser normalizado
	 * @return	mixed			retorna o valor normalizado
	 */
	private function sanitize($value)
	{
		if(is_int($value))
			return (int) $value + '0';
		if(is_double($value) || is_float($value))
			return (double) $value + '0';
		if(is_bool($value))
			return $value ? '1' : '0';
		if(is_string($value) && $value != 'NULL')
			return "'". mysql_real_escape_string($value) ."'";
		if($value == 'NULL')
			return 'NULL';
		return null;
	}
}