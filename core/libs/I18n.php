<?php
/*
 * Copyright (c) 2011-2012, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe de internacionalização
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1.3
 *
 */
class I18n 
{	
	/**
	 * Guarda as mensagens, sendo a chave um MD5 da mensagem original e o valor a mensagem traduzida
	 * @var	array
	 */
	private $messages = array();
	
	/**
	 * Linguagem da tradução
	 * @var	string
	 */
	private $lang;
	
	/**
	 * Linguagem original
	 * @var	string
	 */
	private $default_lang;
	
	/**
	 * Guarda os hooks
	 * @var	array
	 */
	private $hook = array();
	
	/**
	 * Guarda uma instância da própria classe
	 * @var	I18n
	 */
	private static $instance = null;
	
	/**
	 * Construtor da classe
	 * @param	string	$default	linguagem original
	 */
	private function __construct($default)
	{
		$this->default_lang = $default;
	}
	
	/**
	 * Retorna a instância da classes (padrão singleton)
	 * @return	object				retorna a instância de I18n
	 */
	public static function getInstance()
	{
		if(!self::$instance)
			self::$instance = new self(Config::get('default_lang'));
		return self::$instance;
	}
	
	/**
	 * Define a linguagem da tradução
	 * @param	string	$lang		nome da linguagem de tradução
	 * @return	void
	 */
	public function setLang($lang = null)
	{
		if(!$lang)
			$lang = $this->default_lang;
		$this->lang = $lang;
		if($lang != $this->default_lang)
			$this->messages = $this->load($lang);
	}
	
	/**
	 * Traduz uma mensagem e retorna
	 * @param	string	$string			mensagem a ser traduzida
	 * @param	array	$format			array com as variáveis de formatação da mensagem
	 * @throws	TriladoException		disparada caso a mensagem esteja vazia
	 * @return	string					retorna a mensagem traduzida
	 */
	public function get($string, $format = null)
	{
		if(count($string) == 0)
			throw new TriladoException('A string está vazia');
		
		if($this->lang != $this->default_lang)
			$string = $this->messages[md5($string)];
		
		if(is_array($format))
		{
			foreach ($format as $k => $v)
				$string = str_replace('%'.$k, $v, $string);
		}
		foreach ($this->hook as $hook)
			$string = $hook->get($string);
		
		return $string;
	}
	
	/**
	 * Adiciona um hook na lista de hooks
	 * @param	object	$hook	uma instância da uma classe que contenha os métodos do hook
	 * @return	void
	 */
	public function addHook($hook)
	{
		$this->hook[] = $hook;
	}
	
	/**
	 * Carrega um arquivo de tradução pegando as mensagens e traduções e joga em array retornando-o
	 * @param	string	$lang		nome do arquivo
	 * @throws	TriladoException	disparada caso o arquivo não exista ou o conteúdo esteja vazio
	 * @return	array				retorna um array com as mensagens de tradução, sendo as chaves o MD5 da mensagem original
	 */
	private function load($lang)
	{
		$key = 'Trilado.I18n.'. $lang;
		
		$cache = Cache::factory();
		if($cache->has($key))
			return $cache->read($key);
			
		$file_path = ROOT .'app/i18n/'. $lang .'.lang';

		if(!file_exists($file_path))
			throw new FileNotFoundException($file_path);
		$lines = file($file_path);
		if(!count($lines))
			throw new TriladoException('Arquivo "'. $file_path .'" está vazio');
		
		$key = false;
		$result = array();
		foreach($lines as $line)
		{
			$line = trim($line);
			if(preg_match('@^(msgid|msgstr)@', $line))
			{
				if(preg_match('/^msgid "(.+)"/', $line, $match))
					$key = md5($match[1]);
				elseif(preg_match('/^msgstr "(.+)"/', $line, $match))
				{
					if(!$key)
						throw new TriladoException('Erro de sintax no arquivo "'. $file_path .'" na linha "'. $match[1] .'"');
					$result[$key] = $match[1];
					$key = false;
				}
			}
		}
		
		foreach ($this->hook as $hook)
			$result = $hook->load($result);
		
		$cache->write($key, $result, CACHE_TIME);
		
		return $result;
	}
}