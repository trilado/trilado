<?php

/**
 * Classe Helper, auxilia na geração de tags HTML para formulários
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	2
 *
 */
class Form extends Html
{
	public static function json()
	{
		$json2 = file_get_contents('php://input');
		return json_decode($json2);
	}
	
	public static function file()
	{
		$file = new stdClass;
		foreach($_FILES as $k => $v)
			$file->{$k} = $v;
		return $file;
	}
	
	public static function isFile($field)
	{
		return $_FILES[$field]['name'];
	}
	
	/**
	 * Cria um campo input do tipo "text"
	 * @param	string	$name		o nome do campo (os atributos "name" e "id")
	 * @param	mixed	$value		o valor do campo (atributo "value")
	 * @param	array	$attrs		os demais atributos do campo, como por exemplo "onclick", "title" e etc.
	 * @return	string				retorna o HTML do input gerado
	 */
	public static function input($name, $value = null, $attrs = array())
	{
		return self::createTag('input', array_merge(array('name' => $name, 'value' => $value, 'type' => 'text', 'id' => $name), $attrs));
	}
	
	/**
	 * Cria um campo do tipo "textarea"
	 * @param	string	$name		o nome do campo (os atributos "name" e "id")
	 * @param	mixed	$value		o valor do campo
	 * @param	array	$attrs		os demais atributos do campo, como por exemplo "onclick", "title" e etc.
	 * @return	string				retorna o HTML do input gerado
	 */
	public static function textarea($name, $value = null, $attrs = array())
	{
		return self::createTag('textarea', array_merge(array('name' => $name, 'id' => $name), $attrs), false, $value);
	}
	
	/**
	 * Cria um campo input do tipo "hidden"
	 * @param	string	$name		o nome do campo (os atributos "name" e "id")
	 * @param	mixed	$value		o valor do campo (atributo "value")
	 * @param	array	$attrs		os demais atributos do campo, como por exemplo "onclick", "title" e etc.
	 * @return	string				retorna o HTML do input gerado
	 */
	public static function hidden($name, $value = null, $attrs = array())
	{
		return self::createTag('input', array_merge(array('name' => $name, 'value' => $value, 'type' => 'hidden', 'id' => $name), $attrs));
	}
	
	/**
	 * Cria um campo input do tipo "password"
	 * @param	string	$name		o nome do campo (os atributos "name" e "id")
	 * @param	string	$value		o valor do campo (atributo "value")
	 * @param	array	$attrs		os demais atributos do campo, como por exemplo "onclick", "title" e etc.
	 * @return	string				retorna o HTML do input gerado
	 */
	public static function password($name, $value = null, $attrs = array())
	{
		return self::createTag('input', array_merge(array('name' => $name, 'value' => $value, 'type' => 'password', 'id' => $name), $attrs));
	}
	
	/**
	 * Cria um campo input do tipo "submit"
	 * @param	string	$name		o nome do campo (os atributos "name" e "id")
	 * @param	string	$value		o valor do campo (atributo "value")
	 * @param	array	$attrs		os demais atributos do campo, como por exemplo "onclick", "title" e etc.
	 * @return	string				retorna o HTML do input gerado
	 */
	public static function submit($name, $value = null, $attrs = array())
	{
		return Html::createTag('input', array_merge(array('name' => $name, 'value' => $value, 'type' => 'submit', 'id' => $name), $attrs));
	}
	
	/**
	 * Cria um dropdown list (tag "select")
	 * @param	string	$name		o atributo "name", isso também será usado no "id"
	 * @param	array	$options	um array contendo as opções do dropdown, onde a chave é o valor e o valor é o texto
	 * @param	mixed	$selected	o valor da opção que já virá selecionada
	 * @param	array	$attrs		os demais atributos do campo, como por exemplo "onclick", "title" e etc.
	 * @return	string				retorna o HTML do select gerado
	 */
	public static function select($name, $options = array(), $selected = null, $attrs = array())
	{
		$op = '';
		if(is_array($options))
		{
			foreach($options as $v => $t)
			{
				$optionAttrs = array('value' => $v);
				if($selected == $v)
					$optionAttrs['selected'] = 'selected';
				$op .= self::createTag('option', $optionAttrs, false, $t) . "\n";
			}
		}
		return self::createTag('select', array_merge(array('name' => $name, 'id' => $name), $attrs), false, $op);
	}
	
	/**
	 * Cria uma lista de input radios (tag "input" do tipo "radio")
	 * @param	string	$name		o atributo "name", isso também será usado no "id"
	 * @param	array	$options	um array contendo as opções do radio, onde a chave é o valor e o valor é o texto
	 * @param	mixed	$selected	o valor da opção que já virá selecionada
	 * @param	array	$attrs		os demais atributos do campo, como por exemplo "onclick", "title" e etc.
	 * @return	string				retorna o HTML do select gerado
	 */
	public static function radio($name, $options, $selected = null, $attrs = array())
	{
		$radios = '';
		if(is_array($options))
		{
			foreach($options as $v => $t)
			{
				$radioAttrs = array('name' => $name, 'value' => $v, 'type' => 'radio');
				if($v === $selected)
					$radioAttrs['checked'] = 'checked';
				$radios .= self::createTag('input', array_merge($radioAttrs, $attrs)) .' '. $t;
			}
		}
		return $radios;
	}
}
