<?php
/*
 * Copyright (c) 2011-2013, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Contém método para facilitar a importação de arquivos, como controllers, models e helpers
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @author	Diego Oliveira <diegopso2@gmail.com>
 * @author	Jackson Gomes <jackson.souza@gmail.com>
 * @version	1.8
 *
 */
class Import
{
	/**
	 * Carrega um ou mais arquivos a partir de um diretório
	 * 
	 * @param	string	$folder				indica o diretório que serão carregados os arquivos, os valores possíveis são 'core', 'exception', 'controller', 'model' e 'helper'
	 * @param	array	$class				um array com os nomes das classes
	 * @throws	DirectoryNotFoundException	disparada cara o diretório não conste na lista de diretórios padrão
	 * @throws	FileNotFoundException		disparada se o arquivo com o nome da classe não for encontrado
	 * @throws	ClassNotFoundException		disparada se dentro do arquivo não existir a classe
	 * @return	void
	 */
	public static function load($folder, $class = array())
	{
		$folders = array();
		$folders['core']		= 'core/libs/';
		$folders['exception']	= 'core/libs/exceptions/';
		$folders['cachesource']	= 'core/libs/cachesource/';
		$folders['datasource']	= 'core/libs/datasource/';
		$folders['controller']	= 'app/controllers/';
		$folders['model']		= 'app/models/';
		$folders['helper']		= 'app/helpers/';
		$folders['vendor']		= 'app/vendors/';
		
		if(!array_key_exists($folder, $folders))
			throw new DirectoryNotFoundException($folder .'s');
		foreach($class as $c)
		{
			$file = root . $folders[$folder] . $c . '.php';
			if(!file_exists($file))
				throw new FileNotFoundException($folders[$folder] . $c .'.php');
			
			require_once $file;
			
			if(!class_exists($c))
				throw new ClassNotFoundException($c);
		}
	}
	
	/**
	 * Importa as classes específicadas no parâmetro no diretório do núcleo do framework
	 * @param	string	$class1		nome da classe
	 * @param	string	$classN		nome da classe
	 * @return	void
	 */
	public static function core()
	{
		$args = func_get_args();
		self::load('core', $args);
	}
	
	/**
	 * Importa as classes específicadas no parâmetro no diretório dos controllers
	 * @param	string	$class1					nome da classe
	 * @param	string	$classN					nome da classe
	 * @throws	ControllerNotFoundException		disparado se o arquivo com o nome do controller não for encontrado
	 * @throws	ClassNotFoundException			disparado se dentro do arquivo não existir uma classe com o nome do controller
	 * @return	void
	 */
	public static function controller()
	{
		$args = func_get_args();
		foreach($args as $c)
		{
			if(App::$module)
				$file = ROOT . Module::path(App::$module) . 'controllers/' . $c . '.php';
			else
				$file = ROOT . 'app/controllers/' . $c . '.php';
			
			if(!file_exists($file))
			{
				$modules = Config::get('modules');
				$found = false;

				foreach ($modules as $m => $path)
				{
					$file = Module::path($m) . 'controllers/' . $c . '.php';
					if (file_exists($file))
					{
						$found = true;
						break;
					}
				}

				if (!$found)
					throw new ControllerNotFoundException($c);
			}
			
			require_once $file;
			
			if(!class_exists($c))
				throw new ClassNotFoundException($c);
		}
	}
	
	/**
	 * Importa as classes específicadas no parâmetro no diretório dos models
	 * @param	string	$class1		nome da classe
	 * @param	string	$classN		nome da classe
	 * @return	void
	 */
	public static function model()
	{
		$args = func_get_args();
		self::load('model', $args);
	}
	
	/**
	 * Importa as classes específicadas no parâmetro no diretório dos helpers
	 * @param	string	$class1		nome da classe
	 * @param	string	$classN		nome da classe
	 * @return	void
	 */
	public static function helper()
	{
		$args = func_get_args();
		self::load('helper', $args);
	}
	
	/**
	 * Importa uma view específicada
	 * @param	array	$vars			variáveis a serem utilizadas na view
	 * @param	string	$_controller		nome do controller
	 * @param	string	$view			nome da view
	 * @throws	FileNotFoundException	disparado se o arquivo não for encontrado
	 * @return	string					retorna o conteúdo da view
	 */
	public static function view($vars, $_controller, $view)
	{
		ob_start();
		
		extract($vars);
		
		if(App::$module)
		{
			$mobile = ROOT . Module::path(App::$module) .'views/'. $_controller .'/'. $view .'.mobile.php';
			$tablet = ROOT . Module::path(App::$module) .'views/'. $_controller .'/'. $view .'.tablet.php';
		}
		else
		{
			$mobile = ROOT . 'app/views/'. $_controller .'/'. $view .'.mobile.php';
			$tablet = ROOT . 'app/views/'. $_controller .'/'. $view .'.tablet.php';
		}
		
		if(!defined('IS_MOBILE') && !defined('IS_TABLET'))
		{
			$detect = new Mobile_Detect;
			define('IS_MOBILE', $detect->isMobile() && !$detect->isTablet());
			define('IS_TABLET', $detect->isTablet());
		}
		
		if(Config::get('auto_tablet') && IS_TABLET && file_exists($tablet))
		{
			$file = $tablet;
		}
		elseif(Config::get('auto_mobile') && IS_MOBILE && file_exists($mobile))
		{
			$file = $mobile;
		}
		else
		{
			if(App::$module)
				$file = ROOT . Module::path(App::$module) .'views/'. $_controller .'/'. $view .'.php';
			else
				$file = ROOT . 'app/views/'. $_controller .'/'. $view .'.php';
			
			if(!file_exists($file))
				throw new FileNotFoundException('views/'. $_controller .'/'. $view .'.php');
		}
		
		require $file;
		
		return ob_get_clean();
	}
	
	/**
	 * Importa as classes específicadas no parâmetro no diretório dos vendors
	 * @param	string	$class1		nome da classe
	 * @param	string	$classN		nome da classe
	 * @return	void
	 */
	public static function vendor()
	{
		$args = func_get_args();
		self::load('vendor', $args);
	}
	
	/**
	 * Armazena os diretórios para carregamento automático de arquivos de código fonte.
	 * @var	array 
	 */
	private static $directories = array();
	
	/**
	 * Função que importa classes automaticamente, baseado nos diretórios 
	 * registrados pelo método Import::register($dir).
	 * @param	string	$class	Nome da classe a ser carregada.
	 * @return	void
	 */
	public static function autoload($class)
	{
		$key = 'Trilado.Import.Files';
		
		$cache = Cache::factory();
		if(Cache::enabled() && $cache->has($key))
		{
			$files = $cache->read($key);
			if(isset($files[App::$module . $class]))
			{
				require_once $files[App::$module . $class];
				return;
			}
		}
		
		foreach(self::$directories as $dir)
		{
			$file = ROOT . $dir .  $class .'.php';
			if(file_exists($file))
			{
				require_once($file);
				
				$files = $cache->read($key);
				if($files === false)
					$files = array();
				
				$files[App::$module . $class] = $file;
				if(Cache::enabled())
					$cache->write($key, $files, CACHE_TIME);
				
				return;
			}
		}
	}
	
	public static function register_module($module)
	{
		self::register(Module::path($module) . 'models/');
		self::register(Module::path($module) . 'controllers/');
		self::register(Module::path($module) . 'helpers/');
		self::register(Module::path($module) . 'vendors/');
	}
	
	/**
	 * Registra diretórios de arquivos de código fonte para carregamento automático.
	 * @param	string	$dir	diretório a ser inserido, começando da raiz do framework
	 * @return	void
	 */
	public static function register($dir)
	{
		$dir = rtrim($dir, '/') . '/';
		self::$directories[] = $dir;
	}
}
