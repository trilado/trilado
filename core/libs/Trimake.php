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
	
	public function createModel()
	{
		
	}
	
	public function createController($name)
	{
		
	}
	
	public function createView()
	{
		
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
}