<?php
/*
 * Copyright (c) 2013, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */


/**
 * A classe UILoader carrega automaticamente os arquivos CSS e JS
 * 
 * @author	Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version	0.1
 * @license	http://opensource.org/licenses/BSD-3-Clause
 *
 */
class UILoader extends Html
{
	/**
	 * Método interno, é utilizado para checar se houve modificação nos arquivos, caso ocorra, ele faz a substuição
	 * @param	string	$format		formato do arquivo (css, js)
	 * @param	mixed	$files		pode ser uma string com o nome do arquivo ou um array com vários
	 * @param	boolean	$cache		boolean para definir se o arquivo será consultado em cache ou não
	 * @return	string				retorna uma string com o nome do arquivo
	 */
	protected static function load($format, $files, $cache)
	{
		if(!is_array($files))
			$files = array($files);
		
		$key = 'Trilado.UILoader.' . $format . '.' . implode('.', $files);
		
		if($cache)
		{
			$cache = Cache::factory();
			if($cache->has($key))
			{
				if(file_exists($cache->read($key)))
					return $cache->read($key);
			}
		}
		
		$name = 'cache/' . md5(implode('.', $files)) . '.' . strtolower($format);
		
		$timer = self::modified($name, $files);
		if(!$timer['modified'])
			return $name . '?last=' . $timer['timer'];
		
		$data = '';
		$time = '';
		foreach($files as $f)
		{
			if(file_exists($f))
			{
				$time .= filemtime($f);
				$data .= file_get_contents($f) . NL;
			}
			else
			{
				$time .= 0;
				$data .= '/* File not found: ' . $f . ' */' . NL;
			}
		}
		$data = '/*'. $time .'*/' . NL . $data;
		file_put_contents(WWWROOT . $name, $data);
		
		return $name . '?last=' . $time;
	}
	
	/**
	 * Verifica se um arquivo foi modificado
	 * @param	string	$cache		nome do arquivo que está em cache
	 * @param	array	$files		lista com os nomes dos arquivos
	 * @return	array				retorna um array infomado se algum dos arquivos foi modificado e a data de moficação deles
	 */
	protected static function modified($cache, $files)
	{
		$time_old = '';
		if(file_exists($cache))
		{
			$f = fopen($cache, 'r');
			$line = fgets($f);
			fclose($f);
			$matches = array();
			if(preg_match('#/\*(.*)\*/#', $line, $matches))
				$time_old = $matches[1];
		}
		
		$time_new = '';
		foreach($files as $f)
		{
			if(file_exists(WWWROOT . $f))
				$time_new .= filemtime(WWWROOT . $f);
			else
				$time_new .= 0;
		}
		$timer = array();
		$timer['modified'] = $time_old != $time_new;
		$timer['timer'] = $timer['modified'] ? $time_new : $time_old;
		
		return $timer;
	}

	/**
	 * Inclue um ou mais CSS
	 * @param	mixed	$files		pode ser uma string com o nome do arquivo ou um array com vários
	 * @param	boolean	$cache		boolean para definir se o arquivo será consultado em cache ou não
	 * @return	string				retorna o HTML da inclusão do arquivo CSS
	 */
	public static function css($files, $cache = 10)
	{
		return self::createTag('link', array('rel' => 'stylesheet', 'href' => ROOT_VIRTUAL .  self::load('CSS', $files, $cache)), true);
	}
	
	/**
	 * Inclue um ou mais Javascript
	 * @param	mixed	$files		pode ser uma string com o nome do arquivo ou um array com vários
	 * @param	boolean	$cache		boolean para definir se o arquivo será consultado em cache ou não
	 * @return	string				retorna o HTML da inclusão do arquivo Javascript
	 */
	public static function js($files, $cache = 10)
	{
		return self::createTag('script', array('src' => ROOT_VIRTUAL .  self::load('JS', $files, $cache)), false);
	}
	
	public static function less($files, $cache = 10)
	{
		throw new Exception('Método não implementado');
	}
}