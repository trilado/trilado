<?php
/*
 * Copyright (c) 2011-2012, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe principal do Framework, responsável pelo controlar todo o fluxo, fazendo chama de outras classes
 * 
 * @author		Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version		2.5
 *
 */ 
class App 
{
	/**
	 * Guarda os argumentos passados pela URL (prefixo, controller, action e parâmetros)
	 * @var	array
	 */
	private $args = array();
	
	/**
	 * Guarda o nome do controller a ser executado
	 * @var String
	 */
	public static $controller;
	
	/**
	 * Guarda o nome da action a ser executada
	 * @var String
	 */
	public static $action;
	
	/**
	 * Contrutor da classe
	 * @param	string	$url	url acessada pelo usuário
	 */
	public function __construct($url)
	{
		define('CACHE_TIME', 60);
		$cache_config = Config::get('cache');
		if($cache_config['enabled'] && $cache_config['page'])
		{
			$cache = Cache::factory();
			if($cache->has(URL))
			{
				$data = $cache->read(URL);
				exit($data);
			}
		}
		
		$registry = Registry::getInstance();
		
		$this->args = $this->args($url);
		
		//I18n
		define('lang', $this->args['lang']);
		
		define('LANG', $this->args['lang']);
		
		$i18n = I18n::getInstance();
		$i18n->setLang(LANG);
		
		$registry->set('I18n', $i18n);
		
		function __($string, $format = null)
		{
			return I18n::getInstance()->get($string, $format);
		}
		function _e($string, $format = null)
		{
			echo I18n::getInstance()->get($string, $format);
		}
		
		self::$controller = Inflector::camelize($this->args['controller']) .'Controller';
		self::$action = str_replace('-', '_', $this->args['action']);
		
		define('controller', self::$controller );
		define('action', self::$action);
		
		define('CONTROLLER', self::$controller );
		define('ACTION', self::$action);
		
		try
		{
			header('Content-type: text/html; charset='. Config::get('charset'));
			
			Import::core('Controller', 'Template', 'Annotation');
			Import::controller(self::$controller);
		
			$this->controller();
			$this->auth();
			$tpl = new Template();
			$registry->set('Template', $tpl);
			$tpl->render($this->args);
			
			if($cache_config['enabled'] && $cache_config['page'])
			{
				$cache = Cache::factory();
				$data = ob_get_clean();
				$cache->write(URL, $data, $cache_config['time']);
			}
			
			Debug::show();
		}
		catch(PageNotFoundException $e)
		{
			header('HTTP/1.1 404 Not Found');
			Error::render(404, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString(), method_exists($e, 'getDetails') ? $e->getDetails() : '');
			exit;
		}
		catch(Exception $e)
		{
			header('HTTP/1.1 500 Internal Server Error');
			Error::render(500, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString(), method_exists($e, 'getDetails') ? $e->getDetails() : '');
		}
	}
	
	/**
	 * Extrai os argumentos a partir de URL
	 * @param	string	$url	url acessada pelo usuário
	 * @return	array			retorna um array com os argumentos
	 */
	private function args($url)
	{
		$args = Route::exec($url);

		if(empty($args['controller']))
			$args['controller'] = Config::get('default_controller');
		if(empty($args['action']))
			$args['action'] = Config::get('default_action');
		if(empty($args['lang']))
			$args['lang'] = Config::get('default_lang');
		return $args;
	}
	
	/**
	 * Valida o controller requisitado pelo usuário através da URL
	 * @throws	ControllerInheritanceException	dispara se o controller não for subclasse de Controller
	 * @throws	ActionNotFoundException			dispara se a action não existir no controller
	 * @throws	ActionVisibilityException		dispara se a action não estiver como públic
	 * @throws	ActionStaticException			dispara se a action for estática
	 * @throws	PageNotFoundException			dispara se a quantidade obrigatório na action for diferente do esperado
	 * @return	void
	 */
	private function controller()
	{
		if(!is_subclass_of(self::$controller, 'Controller'))
			throw new ControllerInheritanceException(self::$controller);
		
		if(!method_exists(self::$controller, self::$action)) 
			throw new ActionNotFoundException(self::$controller .'->'. self::$action .'()');
		
		$method = new ReflectionMethod(self::$controller, self::$action);
		if(!$method->isPublic()) 
			throw new ActionVisibilityException(self::$controller .'->'. self::$action .'()');
		
		if($method->isStatic()) 
			throw new ActionStaticException(self::$controller .'->'. self::$action .'()');
		
		if(!$this->isValidParams($method))
			throw new PageNotFoundException('A quantidade de parâmetros obrigatórios não conferem');
	}
	
	/**
	 * Verifica se os parâmetros são válidos
	 * @param	object	$method	instância de ReflectionMethod da action requisitada
	 * @return	boolean			retorna true se os parâmetros estiverem certos, ou false no contrário
	 */
	private function isValidParams($method)
	{
		$params = $method->getParameters();
		if(count($this->args['params']) > count($params)) 
			return false;
		if(count($this->args['params']) < count($params))
		{
			$cont = 0;
			foreach ($params as $param)
			{
				if(!$param->isOptional()) 
					$cont++;		
			}
			if(count($this->args['params']) < $cont) 
				return false;
		}
		return true;
	}
	
	/**
	 * Carrega a página de erro de acordo com as configurações e mata a execução
	 * @param	object	$error	instância de Exception
	 * @return	void
	 */
	private function loadError($error)
	{
		Error::render($error->getCode(), $error->getMessage(), $error->getFile(), $error->getLine(), $error->getTraceAsString(), method_exists($error, 'getDetails') ? $error->getDetails() : '');
	}
	
	/**
	 * Verifica se o usuário pode acessar a página de acordo com sua autenticação e a anotação do controller. Se não tiver permissão
	 * é redirecionado para a página de login defina nas configurações
	 * @return	void
	 */
	private function auth()
	{
		$annotation = Annotation::get(self::$controller);
		$roles = null;
		
		if(method_exists(self::$controller, '__construct'))
		{
			$method = new ReflectionMethod(self::$controller, '__construct');
			if($method->isPublic())
			{
				$construct = $annotation->getMethod('__construct');
				if(isset($construct->Auth))
					$roles = $construct->Auth;
			}
		}
		
		$method = $annotation->getMethod(self::$action);
		$auth_action = isset($method->Auth) ? $method->Auth : null;
		
		if($auth_action)
			$roles = $auth_action;
		
		if($auth_action == '*' || (is_array($auth_action) && in_array('*', $auth_action)))
			$roles = null;
		
		if($roles && !is_array($roles))
			$roles = array($roles);
		if($roles)
			call_user_func_array('Auth::allow', $roles);
	}
}
