<?php 
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
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
 * Carrega automaticamente uma classe caso a mesma seja instância e não seja importada ainda
 * @param	string	$class		nome da classe
 * @return	void
 */
function __autoload($class)
{
	$files = array();
	$files[0] = root . 'core/libs/'. $class .'.php';
	$files[1] = root . 'core/libs/exceptions/'. $class .'.php';
	$files[2] = root . 'core/libs/datasource/'. $class .'.php';
	$files[3] = root . 'app/models/'. $class .'.php';
	$files[4] = root . 'app/controllers/'. $class .'.php';
	$files[5] = root . 'app/helpers/'. $class .'.php';
	
	foreach($files as $file)
	{
		if(file_exists($file))
		{
			require_once($file);
			return;
		}
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