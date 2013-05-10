<?php
/*
 * Copyright (c) 2013, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */


/**
 * Contém métodos para capturar dados de uma requisição e para criar um requisição
 * 
 * @author	Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version	0.1
 * @license	http://opensource.org/licenses/BSD-3-Clause
 *
 */
class Request
{
	private function __construct() {}

	/**
	 * Método para auxiliar a utilização do $_POST
	 * 
	 * @param	string	$key		chave 
	 * @param	mixed	$default	valor padrão, caso não exista a variável na chave
	 * @return	mixed				caso exista a chave, retorna o valor, no contrário retorna o padrão informado
	 */
	public static function post($key, $default = null)
	{
		if(isset($_POST[$key]))
			return $_POST[$key];
		return $default;
	}
	
	/**
	 * Método para auxiliar a utilização do $_GET
	 * 
	 * @param	string	$key		chave 
	 * @param	mixed	$default	valor padrão, caso não exista a variável na chave
	 * @return	mixed				caso exista a chave, retorna o valor, no contrário retorna o padrão informado
	 */
	public static function get($key, $default = null)
	{
		if(isset($_GET[$key]))
			return $_GET[$key];
		return $default;
	}
	
	/**
	 * Método para auxiliar a utilização do $_REQUEST
	 * 
	 * @param	string	$key		chave 
	 * @param	mixed	$default	valor padrão, caso não exista a variável na chave
	 * @return	mixed				caso exista a chave, retorna o valor, no contrário retorna o padrão informado
	 */
	public static function request($key, $default = null)
	{
		if(isset($_REQUEST[$key]))
			return $_REQUEST[$key];
		return $default;
	}
	
	/**
	 * Método para auxiliar a utilização do $_REQUEST
	 * 
	 * @param	string	$key		chave
	 * @return	mixed				caso exista a chave, retorna o valor, no contrário retorna o padrão informado
	 */
	public static function file($key)
	{
		if(isset($_FILES[$key]))
			return $_FILES[$key];
	}
	
	/**
	 * Verifica se a requisição é do tipo GET
	 * 
	 * @return	boolean		retorna true se for GET, no contrário retorna false
	 */
	public static function isGet()
	{
		return $_SERVER['REQUEST_METHOD'] === 'GET';
	}
	
	/**
	 * Verifica se a requisição é do tipo POST
	 * 
	 * @return	boolean		retorna true se for POST, no contrário retorna false
	 */
	public static function isPost()
	{
		return $_SERVER['REQUEST_METHOD'] === 'POST';
	}
	
	/**
	 * Verifica se a requisição é AJAX
	 * 
	 * @return	boolean		retorna true se for AJAX, no contrário retorna false
	 */
	public static function isAjax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
	}
	
	/**
	 * Verifica se a requisição é segura HTTPS
	 * 
	 * @return	boolean		retorna true se for segura, no contrário retorna false
	 */
	public static function isSecure()
	{
		return isset($_SERVER['HTTPS']);
	}
	
	/**
	 * 
	 * @return type
	 */
	public static function getContent()
	{
		$json2 = file_get_contents('php://input');
		return json_decode($json2);
	}
	
	/**
	 * Pega o tipo da requisição solicitada pelo usuário
	 * 
	 * @return	string	retorna o tipo da requisição (GET, POST, PUT...)
	 */
	public static function getMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}
	
	/**
	 * 
	 * 
	 * @return	string	retorna o schema da requisição (http ou https)
	 */
	public static function getSchema()
	{
		return isset($_SERVER['HTTPS']) ? 'https' : 'http';
	}
	
	/**
	 * Pega o endereço da página que o usuário estava que o trouxe. Dependendo da 
	 * configuração do servidor essa opção pode não está habilitada
	 * 
	 * @return	string	retorna o endereço de origem da navegação
	 */
	public static function getReferer()
	{
		return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	}

	/**
	 * Pega a porta do servidor
	 * 
	 * @return	int		retorna o número da porta
	 */
	public static function getPort()
	{
		return $_SERVER['SERVER_PORT'];
	}

	/**
	 * Pega o endereço do servidor (domínio ou IP)
	 * 
	 * @return	string	retorna o host do servidor
	 */
	public static function getHost()
	{
		return $_SERVER['HTTP_HOST'];
	}
	
	/**
	 * Pega o endereço completo do site
	 * 
	 * @return	string	retorna o endereço completo do site
	 */
	public static function getSite()
	{
		$url = self::getSchema() .'://'. self::getHost();
		if(self::getPort() != '80' && self::getPort() != '443')
			$url .= ':' . self::getPort();
		return $url . rtrim(ROOT_VIRTUAL, '/') . '/';
	}
	
	/**
	 * Pega o endereço da página
	 * 
	 * @return	string	retorna o endereço completo da página
	 */
	public static function getUri()
	{
		return self::getSite() . (isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'], '/') : '') . (isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
	}
	
	/**
	 * Cria uma requisição HTTP
	 * 
	 * @param	string	$url		endereço da requisição
	 * @param	string	$method		método da requisição (GET, POST, PUT...)
	 * @param	array	$params		lista dos parâmetros
	 * @param	array	$headers	lista dos cabeçalhos
	 * @return	string				retorna o conteúdo do resultado da requisição
	 * @throws Exception			disparada caso a biblioteca cURL não estela habilitada
	 */
	public static function create($url, $method = 'GET', $params = array(), $headers = array(), $curl_options = array())
	{
		if(!function_exists('curl_init'))
			throw new Exception('É necessário habilitar a biblioteca "cURL"');
		
		if(!count($headers) && $method == 'POST')
		{
			$headers = array(
				'Content-type: application/x-www-form-urlencoded', 
				'Content-length: '. strlen(http_build_query($params))
			);
		}
		
		$curl_default_options = array(
			CURLOPT_URL => $url,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_POST => $method == 'POST',
			CURLOPT_POSTFIELDS => http_build_query($params),
			CURLOPT_HTTPHEADER => $headers,
			CURLOPT_USERAGENT => 'Trilado Framework',
		);
		
		foreach ($curl_options as $key => $value) 
			$curl_default_options[$key] = $value;
		
		$curl = curl_init();
		curl_setopt_array($curl, $curl_default_options);

		$response = curl_exec($curl);
		curl_close($curl);
		
		return $response;
	}
}
