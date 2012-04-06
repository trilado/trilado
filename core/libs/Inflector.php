<?php 
/**
 * Classe para manipulação de string.
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	0.1
 *
 */
class Inflector
{
	private function __construct(){}
	
	/**
	 * Converte 'test-controller' para 'TestController'
	 * @param	string	$string		valor a ser convertido
	 * @return	string				valor convertido
	 */
	public static function camelize($string) 
	{
		return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
	}

	/**
	 * Converte 'TestController' para 'test-controller'
	 * @param	string	$string		valor a ser convertido
	 * @return	string				valor convertido
	 */
	public static function uncamelize($string)
	{
		return trim(strtolower(preg_replace("/([A-Z])/", "-$1", $string)), '-');
	}

	/**
	 * Converte 'test-controller' para 'Test Controller'
	 * @param	string	$string		valor a ser convertido
	 * @return	string				valor convertido
	 */
	public static function humanize($string)
	{
		return ucwords(str_replace('-', ' ', $string));
	}

	/**
	 * Converte 'Título de Exemplo' para 'titulo-de-exemplo'
	 * @param	string	$string		valor a ser convertido
	 * @return	string				retorna o valor convertido
	 */
	public static function slugify($string)
	{
		if(charset == 'UTF-8')
			$string = utf8_decode ($string);
		$string = html_entity_decode($string);

		$a = 'ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ';
		$b = 'AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn';
		$string = strtr($string, $a, $b);

		$ponctu = array("?", ".", "!", ",");
		$string = str_replace($ponctu, "", $string);

		$string = trim($string);
		$string = strtolower($string);
		$string = preg_replace('/([^a-z0-9]+)/i', '-', $string);

		if (!empty($string))
			$string = utf8_encode($string);

		return $string;
	}
}