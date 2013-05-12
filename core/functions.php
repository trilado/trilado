<?php 
/*
 * Copyright (c) 2011-2012, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Define algumas funções que serão utilizadas pelo framework 
 */


if(!function_exists('e'))
{
	/**
	 * Imprime um conteúdo
	 * @param	string	$string		valor a ser impresso
	 */
	function e($string)
	{
		echo $string;
	}
}

/**
 * Executa a função print_r com a tag <pre>
 * @param	mixed	$struct		estrutura a ser impressa
 * @return	void
 */
function pr($struct)
{
	echo '<pre>';
	print_r($struct);
	echo '</pre>';
}

/**
 * Executa a função print_r com a tag <pre>
 * @param	mixed	$struct		estrutura a ser impressa
 * @return	void
 */
function pre($struct)
{
	echo '<pre>';
	print_r($struct);
	echo '</pre>';
}

/**
 * Cria e retorna o caractere de tabulação
 * @param	int	$n		quantidade de vezes que desejar dar tabulação
 * @return	string		retorna a tabulação
 */
function tab($n = 1)
{
	return str_repeat("\t", $n);
}

/**
 * Cria e retorna espeçacos em branco
 * @param	int	$n		quantidade de espaços que deseja criar
 * @return	string		retorna os espaços
 */
function t($n = 1)
{
	return str_repeat('&nbsp;', ($n * 5));
}

/**
 * Cria uma instância de stdClass com a propriedade 'd', que recebe o valor informado no parâmetro
 * @param	object	$object		objeto que será valor da propridade 'd'
 * @return	stdClass			retorna uma instância de stdClass
 */
function d($object)
{
	$d = new stdClass;
	$d->d = $object;
	return $d;
}

/**
 * Converte um objeto ou um array em uma string XML
 * @param	mixed	$data		dados a serem convertidos em XML
 * @return	string				retorna uma string XML
 */
function xml_encode($data)
{
	if (!is_array($data) && !is_object($data)) 
		return $data;
		
	$encoded = "\n";
	foreach($data as $k => $d)
	{
		$e = is_string($k) ? $k : 'n';
		$encoded .= "\t<". $e .">". xml_encode($d) ."</". $e .">\n";
	}
	return $encoded . "";
}

/**
 * Codifica os valores de um array ou um objeto em UTF-8
 * @param	mixed	$data		dados a serem convertidos
 * @return	mixed				retorna o array ou objeto convertido
 */
function utf8encode($data)
{
	if(is_string($data))
		return utf8_encode($data);
	if (is_array($data))
	{
		$encoded = array();
		foreach($data as $k => $d)
			$encoded[$k] = utf8encode($d);
		return $encoded;
	}
	if (is_object($data))
	{
		$encoded = new stdClass;
		foreach($data as $k => $d)
			$encoded->{$k} = utf8encode($d);
		return $encoded;
	}
	return $data;
}

/**
 * Decodifica os valores de um array ou objeto de UTF-8
 * @param	mixed	$data	dados a serem decodificados
 * @return	mixed			retorna um objeto ou array sem a codificação UTF-8
 */
function utf8decode($data)
{
	if(is_string($data))
		return utf8_decode($data);
	if (is_array($data))
	{
		$encoded = array();
		foreach($data as $k => $d)
			$encoded[$k] = utf8decode($d);
		return $encoded;
	}
	if(is_object($data))
	{
		$encoded = new stdClass;
		foreach($data as $k => $d)
			$encoded->{$k} = utf8decode($d);
		return $encoded;
	}
	return $data;
}

/**
 * Une dois ou mais array
 * @param	array	$array1		primeiro array
 * @param	array	$array2		segundo array
 * @param	array	$arrayN		enéssimo array
 * @return	array				retorna um array com união dos demais
 */
function array_union()
{
	$args = func_get_args();
	$new_array = array();
	foreach($args as $array)
	{
		foreach($array as $element)
			$new_array[] = $element;
	}
	return $new_array;
}

/**
 * Cria um indentificado único
 * @return	string		retorna o GUID gerado
 */
function guid()
{
	if (function_exists('com_create_guid') === true)
		return trim(com_create_guid(), '{}');
	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

/**
 * Gera uma senha
 * @param	int	$length		tamanho da senha
 * @param	int	$strength	nível se segurança da senha, os valores podem ser 1, 2, 4 e 8, quanto maior, mais segura
 * @return	string			retorna a senha gerada
 */
function new_passwd($length = 8, $strength = 0)
{
	$vowels = 'aeiou';
	$consonants = 'bcdfghjklmnpqrstvwxyz';
	if ($strength & 1)
		$consonants .= 'BCDFGHJKLMNPQRSTVWXYZ';
	if ($strength & 2)
		$vowels .= 'AEIOU';
	if ($strength & 4)
		$consonants .= '123456789';
	if ($strength & 8)
		$consonants .= '@#$%';
 
	$password = '';
	$alt = time() % 2;
	for ($i = 0; $i < $length; $i++)
	{
		if ($alt == 1) 
		{
			$password .= $consonants[(rand() % strlen($consonants))];
			$alt = 0;
		} 
		else 
		{
			$password .= $vowels[(rand() % strlen($vowels))];
			$alt = 1;
		}
	}
	return $password;
}

/**
 * Retorna a Timestamp de uma string de uma data a partir de um formato. Se o 
 * formato não for definido é utilizado o formato padrão definido no arquivo de 
 * configuração Config::get('date_format').
 * 
 * @param String $strDate String contendo a data.
 * @param String $format Formato da data, segue os formatos padrão do PHP.
 * @return int 
 */
function get_timestamp($strDate, $format = false)
{
	if(!$format)
	{
		$format = Config::get('date_format');
	}
	
	$date = DateTime::createFromFormat($format, $strDate);
	return $date->getTimestamp();
}

if (!function_exists('get_called_class'))
{
	/**
	 * http://djomla.blog.com/2011/02/16/php-versions-5-2-and-5-3-get_called_class/
	 */
	function get_called_class($bt = false, $l = 1)
	{
		if (!$bt)
			$bt = debug_backtrace();
		if (!isset($bt[$l]))
			throw new Exception("Cannot find called class -> stack level too deep.");
		if (!isset($bt[$l]['type']))
		{
			throw new Exception('type not set');
		}
		else
		{
			if($bt[$l]['type'] == '::')
			{
				$lines = file($bt[$l]['file']);
				$i = 0;
				$callerLine = '';
				do
				{
					$i++;
					$callerLine = $lines[$bt[$l]['line'] - $i] . $callerLine;
				} while (stripos($callerLine, $bt[$l]['function']) === false);
				
				preg_match('/([a-zA-Z0-9\_]+)::' . $bt[$l]['function'] . '/', $callerLine, $matches);
				
				if (!isset($matches[1])) // must be an edge case.
					throw new Exception("Could not find caller class: originating method call is obscured.");
				
				if($matches[1] == 'self' || $matches[1] == 'parent' )
					return get_called_class($bt, $l + 1);
				else
					return $matches[1];
			}
			elseif($bt[$l]['type'] == '->') // won't get here.
			{
				//if($bt[$l]['function'] == '__get')
				//{
					// edge case -> get class of calling object
					if (!is_object($bt[$l]['object']))
						return $bt[$l]['class'];
					return get_class($bt[$l]['object']);
				/*}
				else
				{
					return $bt[$l]['class'];
				}*/
			}
			else
			{
				throw new Exception("Unknown backtrace method type");
			}
		}
	}
}