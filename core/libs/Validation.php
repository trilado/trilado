<?php
/*
 * Copyright (c) 2011-2012, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe que contém métodos para validação
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	1.2
 *
 */
class Validation
{
	/**
	 * Construtor da classe, é privado para não ser instanciada 
	 */
	private function __construct() {}
	
	/**
	 * Método para inserir uma \ nos elementos de Expressão Regular
	 * @param	string	$value	valor a ser verificado
	 * @return	string			retorna o valor com \ antes dos elementos de Expressão Regular
	 */
	private static function clear($value)
	{
		return preg_replace('/([\\\^\.\$\|\(\)\[\]\*\+\?\{\}\,\#]+)/', '\\\$1', $value);
	}
	
	/**
	 * Método para verificar se um e-mail é válido
	 * @param	string	$email		e-mail a ser verificado
	 * @param	boolean	$checkhost	true para checar se o domínio existe
	 * @return	boolean				retorna true caso o e-mail esteja válido, caso contrário, retorna false
	 */
	public static function email($email, $checkhost = false)
	{
		
	}
	
	/**
	 * Verificar se uma URL é válida
	 * @param	string	$url	URL a ser verificada
	 * @return	boolean			retorna true caso a URL esteja válido, caso contrário, retorna false
	 */
	public static function url($url)
	{
		return preg_match('#^https?://(www\.)?([a-zA-Z0-9\-\.]+)\.([a-z]{2,3})(/[\w\-\.\_]+)*(\?.*)?$#', $url);
	}
	
	/**
	 * Verificar um domínio é válido
	 * @param	string	$url		endereço do domínio
	 * @param	boolean	$checkhost	true para checar se o domínio existe
	 * @return	boolean				retorna true caso o domínio esteja válido, caso contrário, retorna false 
	 */
	public static function site($url, $checkhost = false)
	{
		if(preg_match('#^https?://(www\.)?([a-zA-Z0-9\-\.]+)\.([a-z]{2,3})$#', $value))
		{
			if($checkhost)
			{
				
			}
			return true;
		}
		return false;
	}
	
	/**
	 * Verifica se um caminho do disco é válido
	 * @param	string	$value	endereço do diretório do disco
	 * @return	boolean			retorna true caso o endereço seja válido, no contrário retorna false
	 */
	public static function path($value)
	{
		
	}
	
	/**
	 * Verifica se IP é válido
	 * @param	string	$ip	endereço do IPv4
	 * @return	boolean	retorna true se o endereço for válido, no contrátio retorna false
	 */
	public static function ip($ip)
	{
		return preg_match('#^([1-9]|1[0-9]|1[0-9][0-9]|2[0-9]|2[0-5][0-5])(\.([0-9]|1[0-9]|1[0-9][0-9]|2[0-9]|2[0-5][0-5])){3}$#', $value);
	}
	
	/**
	 * Verifica se uma data é válida
	 * @param	string	$date	data a ser verificada conforme o formato definido na configuração 'date_fomrmat'
	 * @return	boolean			retorna true se a data for válida, no contrário retorna false
	 */
	public static function date($date)
	{
		$date = DateTime::createFromFormat(Config::get('date_format'), $date);
		$date_errors = DateTime::getLastErrors();
		return ($date_errors['warning_count'] + $date_errors['error_count']) === 0;
	}
	
	/**
	 * Verificar um nome de usuário é válida, contendo apenas letras, número e maior que 3 caracteres
	 * @param	string	$value	nome de usuário
	 * @return	boolean			retorna true se o nome de usuário for válido, no contrário retorna false
	 */
	public static function username($value)
	{
		return preg_match('#^([a-zA-Z0-9]{3,})$#', $value);
	}
	
	/**
	 * Verifica se um valor termina com um dos formatos listados (ex.: nome de arquivo)
	 * @param	string	$value		valor a ser verificado
	 * @param	array	$formats	array contendo os formatos (ex.: doc, pdf)
	 * @return	boolean				retorna true se o valor for válido, no contrário retorna false
	 */
	public static function format($value, $formats)
	{
		return preg_match('#\.('. implode('|', $formats) .')$#', $value);
	}
	
	/**
	 * Verifica se um valor é igual ao outro
	 * @param	mixed	$value1	valor
	 * @param	mixed	$value2	valor
	 * @return	boolean			retorna true se os valores forem iguais, no contrário retorna false
	 */
	public static function equals($value1, $value2)
	{
		return $value1 === $value2;
	}
	
	/**
	 * Verifica se um valor está entre um outros dois valores
	 * @param	mixed	$value		valor a ser verificado
	 * @param	mixed	$min		valor menor
	 * @param	mixed	$max		valor maior
	 * @param	boolean	$including	incluir os valores menor e maior (>= e <=)
	 * @return	boolean				retorna true se o valor estiver dentro do intervalor, no contrário retorna false
	 */
	public static function between($value, $min, $max, $including = true)
	{
		if($including)
			return $value >= $min  && $value <= $max;
		return $value > $min  && $value < $max;
	}
	
	/**
	 * Verifica se um valor está dentro do mínimo permitido
	 * @param	mixed	$value	valor
	 * @param	mixed	$min	valor mínimo permitido
	 * @return	boolean			retorna true se o valor estiver dentro do mínimo permitido, no contrário retorna false
	 */
	public static function min($value, $min)
	{
		return $value >= $min;
	}
	
	/**
	 * Verifica se um valor está dentro do máximo permitido
	 * @param	mixed	$value	valor
	 * @param	mixed	$max	valor máximo permitido
	 * @return	boolean			retorna true se o valor estiver dentro do máximo permitido, no contrário retorna false
	 */
	public static function max($value, $max)
	{
		return $value <= $max;
	}
	
	/**
	 * Verificar um CPF é válido
	 * @param	string		$value	CPF (com pontos e hífen)
	 * @return	boolean		retorna true se o CPF for válido, no contrário retorna false
	 */
	public static function cpf($cpf)
	{
		$cpf = str_replace('-', '', $cpf);
				$cpf = str_replace('.', '', $cpf);

		$cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);
		if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') 
			return false;
		else 
		{ 
			for ($t = 9; $t < 11; $t++) 
			{
				for ($d = 0, $c = 0; $c < $t; $c++) 
					$d += $cpf{$c} * (($t + 1) - $c);

				$d = ((10 * $d) % 11) % 10;

				if ($cpf{$c} != $d) 
					return false;
			}
			return true;
		}
	}
	
	/**
	 * Verificar um CNPJ é válido
	 * @param	string		$value	CPF (com pontos e barra)
	 * @return	boolean		retorna true se o CNPJ for válido, no contrário retorna false
	 */
	public static function cnpj($value)
	{
		
	}
	
	/**
	 * Verifica se um valor termina com determinado trecho
	 * @param	string	$value		valor a ser verificado
	 * @param	string	$compare	valor final
	 * @return	boolean				retorna true se o valor terminar com o comparado, no contrário retorna false
	 */
	public static function endsWith($value, $compare)
	{
		return preg_match('#'. self::clear($compare) .'$#', $value);
	}
	
	/**
	 * Verifica se um valor começa com determinado trecho
	 * @param	string	$value		valor a ser verificado
	 * @param	string	$compare	valor inicial
	 * @return	boolean				retorna true se o valor começar com o comparado, no contrário retorna false
	 */
	public static function startsWith($value)
	{
		return preg_match('#^'. self::clear($compare) .'#', $value);
	}
}