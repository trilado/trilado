<?php

class Validation
{
	private function __construct() {}
	
	private static function clear($value)
	{
		return preg_replace('/([\\\^\.\$\|\(\)\[\]\*\+\?\{\}\,\#]+)/', '\\\$1', $value);
	}
	public static function email($email, $checkhost = false)
	{
		
	}
	public static function url($url)
	{
		
	}
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
	public static function path($value)
	{
		
	}
	public static function ip($ip)
	{
		return preg_match('#^([1-9]|1[0-9]|1[0-9][0-9]|2[0-9]|2[0-5][0-5])(\.([0-9]|1[0-9]|1[0-9][0-9]|2[0-9]|2[0-5][0-5])){3}$#', $value);
	}
	public static function date($date)
	{
		
	}
	public static function username($value)
	{
		return preg_match('#^([a-zA-Z0-9]{3,})$#', $value);
	}
	public static function format($value, $formats)
	{
		return preg_match('#('. implode('|', $formats) .')#', $value);
	}
	public static function equals($value1, $value2)
	{
		return $value1 === $value2;
	}
	public static function between($value, $min, $max, $including = true)
	{
		if($including)
			return $value >= $min  && $value <= $max;
		return $value > $min  && $value < $max;
	}
	public static function min($value, $min)
	{
		return $value >= $min;
	}
	public static function max($value, $max)
	{
		return $value <= $max;
	}
	public static function cpf($value)
	{
		
	}
	public static function cnpj($value)
	{
		
	}
	public static function endsWith($value, $compare)
	{
		return preg_match('#'. self::clear($compare) .'$#', $value);
	}
	public static function startsWith($value)
	{
		return preg_match('#^'. self::clear($compare) .'#', $value);
	}
}