<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe principal do Framework, responsável pelo controlar todo o fluxo, fazendo chama de outras classes
 * 
 * @author		Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version		2.1
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
		$this->args = $this->args($url);
		define('is_debug', $this->isDebug());

		if(is_debug)
		{
			error_reporting(E_ALL ^E_NOTICE^E_WARNING);
		}
		else
		{
			/*ini_set('display_errors','Off');
			ini_set('log_errors', 'On');
			ini_set('error_log', ROOT . DS .'logs'. DS .'error.log');*/
		}
		
		//I18n
		define('lang', $this->args['lang']);
		
		$i18n = I18n::getInstance();
		$i18n->setLang(lang);
		
		function __($string, $format = null)
		{
			return I18n::getInstance()->get($string, $format);
		}
		function _e($string, $format = null)
		{
			echo I18n::getInstance()->get($string, $format);
		}
		
		define('controller', camelize($this->args['controller']) .'Controller');
		define('action', str_replace('-', '_', $this->args['action']));
		
		try
		{
			header('Content-type: text/html; charset='. charset);
			
			Import::core('Controller', 'Template', 'Annotation');
			Import::controller(controller, 'MasterController');
		
			$this->controller();
			$this->auth();
			$tpl = new Template();
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
	 * Verifica se o usuário está acessando via rede
	 * @param	string	$ip	IP do usuário
	 * @return	boolean		retorna verdadeiro se o usuário estiver acessando pela rede, no contrário retorna falso
	 */
	private function isNetwork($ip)
	{
		return false;
	}
	
	/**
	 * Verifica se o debug está habilidade para este usuário
	 * @return	boolean		retorna verdadeiro se o debug estiver habilitado
	 */
	private function isDebug()
	{
		if(debug != 'off')
		{
			if(debug == 'local' && (ip == '127.0.0.1' || ip == '::1'))
				return true;
			if(debug == 'all')
				return true;
			if(debug == 'network' && $this->isNetwork(ip))
				return true;
		}
		return false;
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
			throw new PageNotFoundException('');
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
		if(is_debug)
			return require_once root .'core/error/debug.php';
			
		$files[] = root .'app/views/_error/'. $error->getCode() .'.php';
		$files[] = root .'core/error/'. $error->getCode() .'.php';
		foreach($files as $f)
		{
			if(file_exists($f))
				return require_once $f;
		}
		exit('error');	
	}
	
	/**
	 * Verifica se o usuário pode acessar a página de acordo com sua autenticação e a anotação do controller. Se não tiver permissão
	 * é redirecionado para a página de login defina nas configurações
	 * @return	void
	 */
	private function auth()
	{
		$annotation = Annotation::get(controller);
		
		if(method_exists(controller, '__construct'))
		{
			$method = new ReflectionMethod(controller, '__construct');
			if($method->isPublic())
				$roles = $annotation->getMethod('__construct')->Auth;
		}
		if($auth_action = $annotation->getMethod(action)->Auth)
			$roles = $auth_action;
		if($roles && !is_array($roles))
			$roles = array($roles);
		if($roles)
			call_user_func_array('Auth::allow', $roles);
	}
}
