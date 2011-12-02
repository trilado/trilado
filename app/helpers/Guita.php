<?php
/**
 * Classe para auxiliar na criação de CRUD, com métodos parar criar automáticamente SQL, controllers e views.
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	0.1
 *
 */
class Guita
{	
	/**
	 * Gera o SQL de acordo com os models existentes
	 * @return	string		retorna o SQL dos models
	 */
	public function generateTables()
	{
		$models = $this->readModels();
		$sqls = array();
		foreach($models as $m)
		{
			$model = str_replace('.php', '', $m);
			$sqls[] = $this->getTable(new $model());
		}
		return implode(nl, $sqls);
	}
	
	/**
	 * Método para carregar os models
	 * @return	array		retorna uma lista coms os nomes dos models
	 */
	protected function readModels()
	{
		$files = array();
		if ($handle = opendir(root . 'app/models'))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file != '.' && $file != '..')
					$files[] = $file;
			}
			closedir($handle);
		}
		return $files;
	}
	
	/**
	 * Ler um model e gera um SQL referente
	 * @param	Model	$model	instância do model para gerar o SQL
	 * @return	string			retorna o SQL do model
	 */
	protected function getTable(Model $model)
	{
		$annotation = Annotation::get(get_class($model));
		$class = $annotation->getClass();
		
		if($class->Entity)
		{
			$table = new stdClass;
			$table->Name = $class->Entity->Name ? $class->Entity->Name : get_class($model);
			$table->Columns = array();
			$table->Keys = array();
			foreach($model as $field => $value)
			{
				$property = $annotation->getProperty($field);
				if($this->isField($property))
				{
					$column = new stdClass;
					$column->Name = $this->getField($field, $property);
					if(!$column->Name)
						$column->Name = $field;
					$column->Type		= $this->getType($property);
					$column->Nullable	= $this->getNullable($property);
					
					$table->Columns[] = $column;
					if($this->isKey($property))
						$table->Keys[] = $this->getKey($field, $property);
				}
			}
			return $this->getSql($table);
		}
	}
	
	/**
	 * Verifica se uma propriedade do model é um campo no banco de dados
	 * @param	object	$property	anotação da propriedade
	 * @return	boolean				retorna true se for um campo ou false caso contrário
	 */
	protected function isField($property)
	{
		return count((array)$property) > 0;
	}
	
	/**
	 * Verifica se a coluna é uma chave
	 * @param	object	$property	anotação da propriedade
	 * @return	boolean				retorna true se a propriedade for uma chave
	 */
	protected function isKey($property)
	{
		return ($property->Column && $property->Column->Key) || $property->Foreign;
	}
	
	/**
	 * Gera o SQL de criar uma chave estrangeira
	 * @param	string	$field		nome da coluna
	 * @param	object	$property	anotação da propriedade
	 * @return	string				retorna a SQL gerada
	 */
	protected function getKey($field, $property)
	{
		if($property->Foreign)
			return 'FOREIGN KEY(`'. $field .'`) REFERENCES `'. $property->Foreign->Table .'`(`'. $property->Foreign->Column .'`)';
		return 'PRIMARY KEY(`'. $field .'`)';
	}
	
	/**
	 * Pega o nome do coluna a partir da anotação de uma propriedade
	 * @param	string	$field		nome da propriedade
	 * @param	object	$property	anotação da propriedade
	 * @return	string				retorna o nome da coluna
	 */
	protected function getField($field, $property)
	{
		if($property->Column && $property->Column->Name)
			return $property->Column->Name;
		return $field;
	}
	
	/**
	 * Pega o tipo da (SQL) da coluna a partir da anotação da propriedade
	 * @param	object	$property	anotação da propriedade
	 * @return	string				retorna o tipo da coluna
	 */
	protected function getType($property)
	{
		$types = array();
		$types['String']	= 'VARCHAR';
		$types['Int']		= 'INT';
		$types['Boolean']	= 'BIT';
		$types['Date']		= 'DATE';
		$types['DateTime']	= 'DATETIME';
		
		$sizes = array();
		$sizes['String']	= '(255)';
		$sizes['Int']		= '(11)';
		$sizes['Boolean']	= '(1)';
		$sizes['Date']		= '';
		$sizes['DateTime']	= '';
		
		$type = $property->Column->Type;
		if(!$type)
			$type = 'String';
		$size = $property->Column->Size;
		$size = $size ? '('. $size .')' : $sizes[$type];
		return $types[$type] . $size;
	}
	
	/**
	 * Verifica se uma coluna pode ou não ser nula
	 * @param	object	$property	anotação da propriedade
	 * @return	string				retorna o NULL se puder ser nula, no contrário retorna NOT NULL
	 */
	protected function getNullable($property)
	{
		return $property->Required ? ' NOT NULL ' : ' NULL';
	}
	
	/**
	 * Gerar o SQL da tabela
	 * @param	object	$table		instância de stdClass com informações da tabela
	 * @return	string				retorna o código SQL da tabela
	 */
	protected function getSql($table)
	{
		$lines = array();
		foreach($table->Columns as $c)
			$lines[] = tab() . '`'. $c->Name .'` '. $c->Type . $c->Size . $c->DefaultValue . $c->Nullable . $c->Auto;
		foreach($table->Keys as $key)
			$lines[] = tab() . $key;
		return 'CREATE TABLE `'. $table->Name .'`'. nl .'('. nl . implode(','. nl, $lines). nl .');';
	}
	
	/**
	 * Cria os arquivos das classes controllers
	 * @param	boolean	$rewrite		se definido como true sobrescreve os arquivos, casos eles existirem
	 * @throws	GuitaException			disparada caso o arquivo não possa ser sobrescrito
	 * @return	void
	 */
	public function generateControllers($rewrite = false)
	{
		$models = $this->readModels();
		foreach($models as $m)
		{
			$model = str_replace('.php', '', $m);
			$controller = $this->getController(new $model());
			$file = root .'app/controllers/'. $model .'Controller.php';
			if(!file_exists($file) || $rewrite)
			{
				if(file_put_contents($file, $controller) === false)
					throw new GuitaException("Erro ao tentar escrever no arquivo '". $model ."Controller.php'");
			}
		}
	}
	
	/**
	 * Gerar o código de uma classe controller
	 * @param	Model	$model		model do controller
	 * @return	string				retorna o código PHP gerado
	 */
	protected function getController(Model $model)
	{
		$class = get_class($model);
		$array = array_keys((array)$model);
		$controller = "<"."?php". nl ."class {Model}Controller extends Controller". nl ."{". nl ."". tab() ."public function __construct()". nl ."". tab() ."{". nl ."". tab() ."}". nl ."". tab() ."public function index()". nl ."". tab() ."{". nl ."". tab(2) ."\${models} = {Model}::all(1, 10);". nl ."". tab(2) ."\$this->_set('{models}', \${models});". nl ."". tab(2) ."return \$this->_view();". nl ."". tab() ."}". nl ."". tab() ."public function view(\$id)". nl ."". tab() ."{". nl ."". tab(2) ."\${model} = {Model}::get(\$id);". nl ."". tab(2) ."\$this->_set('{model}', \${model});". nl ."". tab(2) ."return \$this->_view();". nl ."". tab() ."}". nl ."". tab() ."public function admin_index(\$p = 1, \$o = '{first_column}', \$t = 'desc')". nl ."". tab() ."{". nl ."". tab(2) ."\${models} = {Model}::all(\$p, 10, \$o, \$t);". nl ."". tab(2) ."\$this->_set('{models}', \${models});". nl ."". tab(2) ."return \$this->_view();". nl ."". tab() ."}". nl ."". tab() ."public function admin_create()". nl ."". tab() ."{". nl ."". tab(2) ."if(is_post)". nl ."". tab(2) ."{". nl ."". tab(3) ."\${model} = \$this->_data(new Model());". nl ."". tab(3) ."try". nl ."". tab(3) ."{". nl ."". tab(4) ."{Model}::save(\${model});". nl ."". tab(4) ."\$this->_flash('success', '{Model} inserido com sucesso!');". nl ."". tab(4) ."\$this->_redirect('~/{model}/');". nl ."". tab(3) ."}". nl ."". tab(3) ."catch(ValidateException \$e)". nl ."". tab(3) ."{". nl ."". tab(4) ."\$this->_flash('error', \$e->getMessage());". nl ."". tab(4) ."\$this->_set('{model}', \${model});". nl ."". tab(3) ."}". nl ."". tab(2) ."}". nl ."". tab(2) ."return \$this->_view();". nl ."". tab() ."}". nl ."". tab() ."public function admin_edit(\$id)". nl ."". tab() ."{". nl ."". tab(2) ."\${model} = {Model}::get(\$id);". nl ."". tab(2) ."if(\${model})". nl ."". tab(2) ."{". nl ."". tab(3) ."if(is_post)". nl ."". tab(3) ."{". nl ."". tab(4) ."\${model} = \$this->_data(\${model});". nl ."". tab(4) ."try". nl ."". tab(4) ."{". nl ."". tab(4) ."". tab() ."{Model}::save(\${model});". nl ."". tab(4) ."". tab() ."\$this->_flash('success', '{Model} atualizado com sucesso!');". nl ."". tab(4) ."". tab() ."\$this->_redirect('~/{model}/');". nl ."". tab(4) ."}". nl ."". tab(4) ."catch(ValidateException \$e)". nl ."". tab(4) ."{". nl ."". tab(4) ."". tab() ."\$this->_flash('error', \$e->getMessage());". nl ."". tab(4) ."". tab() ."\$this->_set('{model}', \${model});". nl ."". tab(4) ."}". nl ."". tab(3) ."}". nl ."". nl ."". tab(2) ."}". nl ."". tab(2) ."else". nl ."". tab(2) ."{". nl ."". tab(3) ."\$this->_flash('error', '{Model} não encontrado!');". nl ."". tab(3) ."\$this->_redirect('~/{model}/');". nl ."". tab(2) ."}". nl ."". tab(2) ."return \$this->_view();". nl ."". tab() ."}". nl ."". tab() ."public function admin_delete()". nl ."". tab() ."{". nl ."". tab(2) ."\${model} = {Model}::get(\$id);". nl ."". tab(2) ."if(\${model})". nl ."". tab(2) ."{". nl ."". tab(3) ."try". nl ."". tab(3) ."{". nl ."". tab(4) ."{Model}::delete(\${model});". nl ."". tab(4) ."\$this->_flash('success', '{Model} deletado com sucesso!');". nl ."". tab(3) ."}". nl ."". tab(3) ."catch(ValidateException \$e)". nl ."". tab(3) ."{". nl ."". tab(4) ."\$this->_flash('error', \$e->getMessage());". nl ."". tab(3) ."}". nl ."". tab(2) ."}". nl ."". tab(2) ."else". nl ."". tab(2) ."{". nl ."". tab(3) ."\$this->_flash('error', '{Model} não encontrado!');". nl ."". tab(2) ."}". nl ."". tab(2) ."\$this->_redirect('~/{model}/');". nl ."". tab() ."}". nl ."}";
		$controller = str_replace('{Model}', $class, $controller);
		$controller = str_replace('{model}', strtolower($class), $controller);
		$controller = str_replace('{models}', strtolower($class) .'s', $controller);
		$controller = str_replace('{first_column}', $array[0], $controller);
		return $controller;
	}
	
	/**
	 * Cria os arquivos HTML das views
	 * @param	boolean $rewrite		se definido como true, sobrescreve os arquivos caso eles existirem
	 * @throws	GuitaException			disparada caso o arquivo não possa ser sobrescrito
	 * @return	void
	 */
	public function generateViews($rewrite = false)
	{
		$models = $this->readModels();
		foreach($models as $m)
		{
			$model = str_replace('.php', '', $m);
			$crud = array('index','view','admin_index', 'admin_create');
			
			if(!is_dir(root .'app/views/'. uncamelize($model)))
				mkdir(root .'app/views/'. uncamelize($model));
			
			foreach($crud as $c)
			{
				$view = $this->getView(new $model(), $c);
				$file = root .'app/views/'. uncamelize($model) .'/'. $c .'.php';
				if(!file_exists($file) || $rewrite)
				{
					if(file_put_contents($file, $view) === false)
						throw new GuitaException("Erro ao tentar escrever no arquivo '". uncamelize($model) .'/'. $c .".php'");
				}
			}
		}
	}
	
	/**
	 * Gera e retorna HTML de um view
	 * @param	Model	$model		model a ser gerado uma view
	 * @param	string	$type		tipo de view, pode assumir os valores: index, view, admin_index e admin_create
	 * @return	string				retorna o HTML gerado
	 */
	protected function getView(Model $model, $type)
	{
		if($type == 'index')
			return $this->getViewIndex($model);
		elseif($type == 'view')
			return $this->getViewView($model);
		elseif($type == 'admin_index')
			return $this->getViewList($model);
		elseif($type == 'admin_create')
			return $this->getViewCreate($model);
	}
	
	/**
	 * Gera e retorna o código HTML da view 'admin_create'
	 * @param	Model	$model		instância do model
	 * @return	string				retorna o HTML gerado
	 */
	protected function getViewCreate(Model $model)
	{
		$class = get_class($model);
		$view = '<form method="POST" action="">'. nl;
		foreach($model as $property => $value)
		{
			if($this->isField($property))
			{
				$label = $property->Label ? $property->Label : ($property->Column ? $property->Column : $property);
				$field = $property->Column ? $property->Column : $property;
				$view .= tab() .'<div class="">'. nl;
				$view .= tab(1) .'<label for="'. $property->Column .'">'. $label .'</label>'. nl;
				$view .= tab(1) .'<?php echo Form::input("'. $field .'", $'. strtolower($class) .'->'. $field .') ?>'. nl;
				$view .= tab() .'</div>'. nl;
			}
		}
		$view .= tab() .'<input type="submit" value="Save" />'. nl;
		$view .= '</form>';
		return $view;
	}
	
	/**
	 * Gera e retorna o código HTML da view 'admin_index'
	 * @param	Model	$model	instância do model
	 * @return	string			retorna o HTML gerado
	 */
	protected function getViewList(Model $model)
	{
		$class = get_class($model);
		$view = '<table>'. nl;
		$view .= tab() .'<thead>'. nl;
		$view .= tab(2) .'<tr>'. nl;
		foreach($model as $property => $value)
		{
			if($this->isField($property) && !$this->isKey($property))
			{
				$label = $property->Label ? $property->Label : ($property->Column ? $property->Column : $property);
				$view .= tab(3) .'<th>'. $label .'</th>'. nl;
			}
		}
		$view .= tab(2) .'</tr>'. nl;
		$view .= tab() .'</thead>'. nl;
		$view .= tab() .'</body>'. nl;
		$view .= tab(2) .'<?php foreach($'. strtolower($class) .'s as $'. strtolower($class) .'): ?>'. nl;
		$view .= tab(2) .'<tr>'. nl;
		foreach($model as $property => $value)
		{
			if($this->isField($property) && !$this->isKey($property))
			{
				$field = $property->Column ? $property->Column : $property;
				$view .= tab(3) .'<td><?php echo $'. strtolower($class) .'->'. $field .' ?></td>'. nl;
			}
		}
		$view .= tab(2) .'</tr>'. nl;
		$view .= tab(2) .'<?php endforeach ?>'. nl;
		$view .= tab() .'</tbody>'. nl;
		$view .= '</table>';
		return $view;
	}
	
	/**
	 * Gera e retorna o código HTML da view 'index'
	 * @param	Model	$model	instância do model
	 * @return	string			retorna o HTML gerado
	 */
	protected function getViewIndex(Model $model)
	{
		$class = get_class($model);
		$view = '<table>'. nl;
		$view .= tab() .'<thead>'. nl;
		$view .= tab(2) .'<tr>'. nl;
		foreach($model as $property => $value)
		{
			if($this->isField($property) && !$this->isKey($property))
			{
				$label = $property->Label ? $property->Label : ($property->Column ? $property->Column : $property);
				$view .= tab(3) .'<th>'. $label .'</th>'. nl;
			}
		}
		$view .= tab(2) .'</tr>'. nl;
		$view .= tab() .'</thead>'. nl;
		$view .= tab() .'</body>'. nl;
		$view .= tab(2) .'<?php foreach($'. strtolower($class) .'s as $'. strtolower($class) .'): ?>'. nl;
		$view .= tab(2) .'<tr>'. nl;
		foreach($model as $property => $value)
		{
			if($this->isField($property) && !$this->isKey($property))
			{
				$field = $property->Column ? $property->Column : $property;
				$view .= tab(3) .'<td><?php echo $'. strtolower($class) .'->'. $field .' ?></td>'. nl;
			}
		}
		$view .= tab(2) .'</tr>'. nl;
		$view .= tab(2) .'<?php endforeach ?>'. nl;
		$view .= tab() .'</tbody>'. nl;
		$view .= '</table>';
		return $view;
	}
	
	/**
	 * Gera e retorna o código HTML da view 'view'
	 * @param	Model	$model	instância do model
	 * @return	string			retorna o HTML gerado
	 */
	protected function getViewView(Model $model)
	{
		$class = get_class($model);
		$view = '<div>'. nl;
		foreach($model as $property => $value)
		{
			if($this->isField($property))
			{
				$label = $property->Label ? $property->Label : ($property->Column ? $property->Column : $property);
				$field = $property->Column ? $property->Column : $property;
				$view .= tab() .'<p><b>'. $field .':</b> <?php echo $'. strtolower($class) .'->'. $field .' ?></p>'. nl;
			}
		}
		$view .= '</div>';
		return $view;
	}
}
