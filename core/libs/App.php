<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe principal do Framework, responsável pelo controlar todo o fluxo, fazendo chama de outras classes
 * 
 * @author		Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version		2.2
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
		$registry = Registry::getInstance();
		
		$this->args = $this->args($url);
		
		//I18n
		define('lang', $this->args['lang']);
		
		$i18n = I18n::getInstance();
		$i18n->setLang(lang);
		
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
		
		try
		{
			header('Content-type: text/html; charset='. charset);
			
			Import::core('Controller', 'Template', 'Annotation');
			Import::controller(controller);
		
			$this->controller();
			$this->auth();
			$tpl = new Template();
			$registry->set('Template', $tpl);
			$tpl->render($this->args);
			//cache
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
			$args['controller'] = default_controller;
		if(empty($args['action']))
			$args['action'] = default_action;
		if(empty($args['lang']))
			$args['lang'] = default_lang;
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
		if(!is_subclass_of(controller, 'Controller'))
			throw new ControllerInheritanceException(controller);
		
		if(!method_exists(controller, action)) 
			throw new ActionNotFoundException(controller .'->'. action .'()');
		
		$method = new ReflectionMethod(controller, action);
		if(!$method->isPublic()) 
			throw new ActionVisibilityException(controller .'->'. action .'()');
		
		if($method->isStatic()) 
			throw new ActionStaticException(controller .'->'. action .'()');
		
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
		$annotation = Annotation::get(controller);
		$roles = null;
		
		if(method_exists(controller, '__construct'))
		{
			$method = new ReflectionMethod(controller, '__construct');
			if($method->isPublic())
			{
				$construct = $annotation->getMethod('__construct');
				if(isset($construct->Auth))
					$roles = $construct->Auth;
			}
		}
		
		$method = $annotation->getMethod(action);
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
