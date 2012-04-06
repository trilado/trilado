<?php
/**
 * Classe Helper, auxilia na geração de tags HTML
 *
 * @author		Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version		2
 *
 */
class Html 
{
	/**
	 * Gera uma tag HTML com base nos parâmetros
	 * @param	string	$tag		nome da tag
	 * @param	array	$attrs		atributos da tag
	 * @param	boolean	$close		indica se a tag é auto fechável ou não (ex.: <tag></tag> ou <tag />)
	 * @param	mixed	$value		valor da tag
	 * @return	string				retorna o HTML gerado
	 */
	public static function createTag($tag, $attrs = array(), $close = true, $value = null)
	{
		$html = '<'. $tag;
		foreach($attrs as $n => $v)
			$html .= ' '. $n .'="'. $v .'"';
		return $html .= $close ? ' />' : '>'.$value.'</'.$tag.'>';
	}
}
