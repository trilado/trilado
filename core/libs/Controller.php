<?php/* * Copyright (c) 2011, Valdirene da Cruz Neves J�nior <linkinsystem666@gmail.com> * All rights reserved. *//** * A classe Controller, deve ser herdada por todos os controllers criados pelo programador da aplica��o, possui v�rios m�todos * que ser�o utilizados pelo mesmo, sendo que a maioria deles n�o podem ser sobrescritos *  * @author		Valdirene da Cruz Neves J�nior <linkinsystem666@gmail.com> * @version		2 * */ abstract class Controller{	/**	 * Guarda a vari�veis definidas pelo usu�rio a serem passadas para a view	 * @var array	 */	protected $_vars = array();		/**	 * Cria uma vari�vel para a view. N�o pode ser sobrescrito	 * @param string $var		nome da vari�vel	 * @param mixed $value		valor da vari�vel	 * @return void	 */	final protected function _set($var, $value)	{		$this->_vars[$var] = $value;	}		/**	 * Define qual view ser� chamada. � utilizado como retorno na action. N�o pode ser sobrescrito	* @param mixed $param1		pode assumir os dados da vari�vel $model, o nome da view ou o nome do controller	 * @param mixed $param2		pode assumir os dados da vari�vel $model ou nome da view	 * @param mixed $param3		dados que v�o para a view na vari�vel '$model'	 * @return object				retorna uma inst�ncia de stdClass com informa��es para renderiza��o	 */	final protected function _view($param1 = null, $param2 = null, $param3 = null)	{		return $this->_return('view', $this->_file($param1, $param2, $param3));	}		/**	 * Imprime um texto da tela e mata a execu��o. N�o pode ser sobrescrito	 * @param mixed $data	texto a ser impresso	 */	final protected function _print($data)	{		exit($data);	}		/**	 * Define um conte�do a ser impresso no miolo do template na renderia��o. Deve ser utilizado como returno da action. N�o pode ser sobrescrito	 * @param string $data		valor a ser impresso	 * @return object				retorna uma inst�ncia de stdClass com informa��es para renderiza��o	 */	final protected function _content($data)	{		return $this->_return('content', $data);	}		/**	 * Define uma view a ser impressa sem utiliza��o de template. Deve ser utilizado como retorno da action. N�o pode ser sobrescrito	 * @param mixed $param1		pode assumir os dados da vari�vel $model, o nome da view ou o nome do controller	 * @param mixed $param2		pode assumir os dados da vari�vel $model ou nome da view	 * @param mixed $param3		dados que v�o para a view na vari�vel '$model'	 * @return object				retorna uma inst�ncia de stdClass com informa��es para renderiza��o	 */	final protected function _page($param1 = null, $param2 = null, $param3)	{		return $this->_return('page', $this->_file($param1, $param2, $param3));	}		/**	 * Define um snippet a ser impresso como miolo do template na renderiza��o. Deve ser utilizado como retorno da action. N�o pode ser sobrescrito	 * @param string $view		nome do snippet	 * @param mixed $data		dados a serem passados para o snippet	 * @return object			retorna uma inst�ncia de stdClass com informa��es para renderiza��o	 */	final protected function _snippet($view, $data = null)	{		$this->_set('model', $data);		return $this->_return('snippet', array('controller' => '_snippet', 'view' => strtolower($view)));	}		/**	 * Define um json a ser impresso na respota da requisi��o. Deve ser utilizado como retorno da action. N�o pode ser sobrescrito	 * @param midex $data		dados a serem transformados em json	 * @return object			retorna uma inst�ncia de stdClass com informa��es para renderiza��o	 */	final protected function _json($data)	{		return $this->_return('json', $data);	}		/**	 * Define um xml a ser impresso na respota da requisi��o. deve ser utilizado como retorno da action. N�o pode ser sobrescrito	 * @param mixed $data		dados a serem tranformados em xml	 * @return object			retorna uma inst�ncia de stdClass com informa��es para renderiza��o	 */	final protected function _xml($data)	{		return $this->_return('xml', $data);	}		/**	 * Redireciona a requisi��o para outra p�gina	 * @param string $param1		se for passado sozinho e inicioar "~/" define a URL (dentro da aplica��o) na qual ser� redirecionada,	 * 								caso seja um string, define o nome da action dentro do controller que ser� redicionada, se for passado	 * 								junto com segundo par�metro, define o nome do controller	 * @param string $param2		define o nome da action que ser� redirecionada dentro do controller informado no primeiro par�metro	 * @return void	 */	final protected function _redirect($param1, $param2 = '')	{		if(preg_match('@^~/(.*)@', $param1))			$this->_location(root_virtual . trim($param1, '~/'));		if($param1 && !$param2)			$this->_location(root_virtual . controller .'/'. $param1);		if($param1 && $param2)			$this->_location(root_virtual . $param1 .'/'. $param2);	}		/**	 * M�todo que executa a fun��o de redirecionadomento	 * @param string $location		local do redirecionamento	 * @return void	 */	final private function _location($location)	{		header('Location: '. $location);		exit;	}		/**	 * Define uma flash message a ser exibida na view, normalmente utilizada para informar se uma opera��o foi executada com �xito ou se ocorreu erro	 * @param string $type		classe (CSS) do elemento a ser gerado	 * @param string $msg		mensagem	 * @return void	 */	final protected function _flash($type, $msg)	{		Session::set('Flash.Message', array('type' => $type, 'message' => $msg));	}		/**	 * 	 */	final protected function _args()	{			}		/**	 * Verifica e retorna qual view ser� renderizada	 * @param mixed $param1		pode assumir os dados da vari�vel $model, o nome da view ou o nome do controller	 * @param mixed $param2		pode assumir os dados da vari�vel $model ou nome da view	 * @param mixed $param3		dados que v�o para a view na vari�vel '$model'	 * @return array			returna um array contendo o nome do controller e view que ser�o renderizados	 */	final private function _file($param1 = null, $param2 = null, $param3 = null)	{		if($param3) //$this->_view('user', 'create', array());		{			$this->_set('model', $param3);			$file = array('controller' => uncamelize($param1), 'view' => strtolower($param2));		}		elseif($param2)		{			if(is_string($param2)) //$this->_view('user', 'create');			{				$file = array('controller' => uncamelize($param1), 'view' => strtolower($param2));			}			else //$this->_view('create', array());			{				$this->_set('model', $param2);				$file = array('controller' => uncamelize(str_replace('Controller', '', controller)), 'view' => strtolower($param1));			}		}		elseif($param1)		{			if(is_string($param1)) //$this->_view('create');			{				$file = array('controller' => uncamelize(str_replace('Controller', '', controller)), 'view' => strtolower($param1));			}			else //$thid=>_view(array());			{				$this->_set('model', $param1);				$file = array('controller' => uncamelize(str_replace('Controller', '', controller)), 'view' => strtolower(action));			}		}		else //$thid=>_view();		{			$file = array('controller' => uncamelize(str_replace('Controller', '', controller)), 'view' => strtolower(action));		}		return $file;	}		/**	 * Carrega os dados de uma requisi��o POST para uma inst�ncia de um model	 * @param object $model		inst�ncia de um Model	 * @return object			retorna a inst�ncia do model informado no par�metro ou de stdClass	 */	final protected function _data($model = null)	{		if(!$model)		{			$model = new stdClass;			foreach($_POST as $k => $v)				$model->{$k} = $v;		}		else		{			$annotation = Annotation::get(get_class($model));			foreach($model as $k => $v)			{				if($_POST[$k] !== null)				{					$property = $annotation->getProperty($k);					if((count((array)$property) > 0) && !$property->AutoGenerated)					{												$type = strtolower($property->Column->Type);						$type = $type == 'double' ? 'float' : $type;						$type = $type == 'int' ? 'integer' : $type;						$value = $_POST[$k];						settype($value, $type);						$model->{$k} = $value;					}				}			}		}		return $model;	}		/**	 * Cria um objeto com informa��es para renderiza��o	 * @param string $type		tipo de renderiza��o	 * @param mixed $data		dados da renderiza��o	 * @return object			retorna uma inst�ncia de stdClass	 */	final private function _return($type, $data)	{		$ob = new stdClass;		$ob->Type = $type;		$ob->Data = $data;		$ob->Vars = $this->_vars;		return $ob;	}	public function beforeRender()	{	}	public function afterRender()	{	}	public function __destruct()	{		}}