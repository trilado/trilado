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
	 * Contrutor da classe
	 * @param	string	$url	url acessada pelo usuário
	 */
	public function __construct($url)
	{
		define('CACHE_TIME', 60);
		$cache_config = Config::get('cache');
		if($cache_config['page'])
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
		
		define('controller', Inflector::camelize($this->args['controller']) .'Controller');
		define('action', str_replace('-', '_', $this->args['action']));
		
		define('CONTROLLER', Inflector::camelize($this->args['controller']) .'Controller');
		define('ACTION', str_replace('-', '_', $this->args['action']));
		
		try
		{
			header('Content-type: text/html; charset='. Config::get('charset'));
			
			Import::core('Controller', 'Template', 'Annotation');
			Import::controller(CONTROLLER);
		
			$this->controller();
			$this->auth();
			$tpl = new Template();
			$registry->set('Template', $tpl);
			$tpl->render($this->args);
			
			if($cache_config['page'])
			{
				$cache = Cache::factory();
				$data = ob_get_clean();
				$cache->write(URL, $data, $cache_config['time']);
			}
			
			Debug::show();
		}
		catch(PageNotFoundException $e)
		{
			header('HTTP/1.1 404 Not Found');;
			$this->loadError($e);
			exit;
		}
		catch(Exception $e)
		{
			header('HTTP/1.1 500 Internal Server Error');
			$this->loadError($e);
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
		if(!is_subclass_of(CONTROLLER, 'Controller'))
			throw new ControllerInheritanceException(CONTROLLER);
		
		if(!method_exists(CONTROLLER, ACTION)) 
			throw new ActionNotFoundException(CONTROLLER .'->'. ACTION .'()');
		
		$method = new ReflectionMethod(CONTROLLER, ACTION);
		if(!$method->isPublic()) 
			throw new ActionVisibilityException(CONTROLLER .'->'. ACTION .'()');
		
		if($method->isStatic()) 
			throw new ActionStaticException(CONTROLLER .'->'. ACTION .'()');
		
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
		$annotation = Annotation::get(CONTROLLER);
		$roles = null;
		
		if(method_exists(CONTROLLER, '__construct'))
		{
			$method = new ReflectionMethod(CONTROLLER, '__construct');
			if($method->isPublic())
			{
				$construct = $annotation->getMethod('__construct');
				if(isset($construct->Auth))
					$roles = $construct->Auth;
			}
		}
		
		$method = $annotation->getMethod(ACTION);
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
