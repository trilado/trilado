<?php
/*
 * Copyright (c) 2013, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */


/**
 * Classe para gerar, automaticamente, operações CRUD
 * 
 * @author		Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version		0.1
 *
 */ 
class Trimake
{
	public function createModule($name)
	{
		$module = ROOT . 'app/modules/' . $name . '/';
		$this->createDir($module);
		$this->createDir($module . 'controllers/');
		$this->createDir($module . 'models/');
		$this->createDir($module . 'views/');
	}
	
	public function createModel($name)
	{
		$struct = $this->loadTable($name);
		
	}
	
	public function createController($model, $module = '')
	{
		$file = ROOT . 'app/' . $module . 'controllers/' . $model . 'Controller.php';
		$content = $this->loadTemplate('controller', array('Name' => $model));
		if(!file_put_contents($file, $content))
			throw new Exception ('Erro ao tentar criar o arquivo "' . $file . '"');
	}
	
	public function createViews($model, $module = '')
	{
		$this->createView($model, 'index', $module);
		$this->createView($model, 'view', $module);
		$this->createView($model, 'admin_index', $module);
		$this->createView($model, 'admin_add', $module);
	}
	
	public function createView($model, $type, $module = '')
	{
		$file = ROOT . 'app/' . $module . 'views/' . $model . '/' . $type . 'php';
		$content = $this->loadTemplate('view', array('Name' => $model));
		if(!file_put_contents($file, $content))
			throw new Exception ('Erro ao tentar criar o arquivo "' . $file . '"');
	}
	
	public function createTable()
	{
		
	}
	
	protected function createDir($path)
	{
		if(!is_dir($path))
		{
			if(!mkdir($path, 0755))
				throw new Exception('Erro ao tentar criar o diretório "' . $path . '"');
		}
	}
	
	protected function loadTable($name)
	{
		$db = Database::factory();
		
	}
	
	protected function loadTemplate($tpl, $vars = array())
	{
		$config = Config::get('trimake');
		$file = ROOT . 'app/views/' . $config['template'] . '/' . $tpl;
		if(!file_exists($file))
			throw new Exception('Arquivo "' . $file . '" não encontrado');
		
		$content = file_get_contents($file);
		
		foreach ($vars as $key => $value)
			$content = str_replace('{' . $key . '}', $value, $content);
		return $content;
	}
}