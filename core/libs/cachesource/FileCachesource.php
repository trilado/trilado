<?php
/*
 * Copyright (c) 2012, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */


/**
 * Classe para manipulação de cache em disco
 * 
 * @author		Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @author		Diego Oliveia <diegopso2@gmail.com>
 * @version		1.2
 *
 */ 
class FileCachesource extends Cachesource
{
	/**
	 * Guarda uma instância da própria classe
	 * @var	FileCachesource 
	 */
	private static $instance = null;
	
	/**
	 * Guarda o cache da memória após ler do disco
	 * @var	array
	 */
	private static $data = array();

	/**
	 * Identificador de grupos de cache. 
	 */
	const GROUP_ID = 'TRILADO_FILE_GROUP';
	
	/**
	 * Construtor da classe, é protegido para não ser instanciada 
	 */
	protected function __construct() {}
	
	/**
	 * Método para instanciação do classe
	 * @return	FileCachesource		retorna a instância da classe FileCachesource
	 */
	public static function getInstance()
	{
		if(!self::$instance)
			self::$instance = new self();
		return self::$instance;
	}
	
	/**
	 * Retorna o endereço do arquivo no disco de acordo com a chave
	 * @param	string	$key	chave do cache
	 * @return	string			retorna o endereço completo do arquivo do disco
	 */
	private function file($key)
	{
		return root . 'app/tmp/cache/' . md5($key);
	}
	
	/**
	 * Escreve dados no cache
	 * @param	string	$key	chave em que será gravado o cache
	 * @param	mixed	$data	dados a serem gravados
	 * @param	int		$time	tempo, em minutos, que o cache existirá
	 * @return	boolean			retorna true se o cache for gravado com sucesso, no contrário, retorna false
	 */
	public function write($key, $data, $time = 1)
	{
		$file = array();
		$file['time'] = time() + ($time * minute);
		$file['data'] = $data;
		
		self::$data[md5($key)] = $file;
		
		$status = file_put_contents($this->file($key), serialize($file));
		return $status !== false;
	}
	
	/**
	 * Ler e retorna os dados do cache
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	mixed			retorna os dados se o cache existir, no contrário retorna false (use !== false)
	 */
	public function read($key)
	{
		if(isset(self::$data[md5($key)]))
		{
			$file = self::$data[md5($key)];
			if(((int)$file['time']) > time())
				return $file['data'];
		}
		
		if(file_exists($this->file($key)))
		{
			$file = unserialize(file_get_contents($this->file($key)));
			if(is_array($file))
			{
				if(isset($file['time']) && isset($file['data']) && ((int)$file['time']) > time())
				{
					self::$data[md5($key)] = $file;
					return $file['data'];
				}
			}
		}
		return false;
	}
	
	/**
	 * Remove um cache específico
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	boolean			retorna true se o cache foi removido com sucesso, no contrário retorna false
	 */
	public function delete($key)
	{
		if(file_exists($this->file($key)))
			return unlink($this->file($key));
		return true;
	}
	
	/**
	 * Remove todos os dados do cache
	 * @return	void 
	 */
	public function clear()
	{
		$dir = opendir(root . 'app/tmp/cache/');
		while(false !== ($file = readdir($dir)))
		{
			if($file != '.' && $file != '..') 
			{
				chmod($this->file($file), 0777);
				if(!is_dir($this->file($file)))
					unlink($this->file($file));
			}
		}
		closedir($dir);
	}

	/**
	 * Verifica se um cache existe
	 * @param	string	$key	chave em que o cache foi gravado
	 * @return	boolean			retorna true se o cache existir, no contrário retorna false 
	 */
	public function has($key)
	{
		return $this->read($key) !== false;
	}
}