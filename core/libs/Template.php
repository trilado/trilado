<?php
/*
 * Copyright (c) 2011, Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * All rights reserved.
 */


/**
 * Classe responsável por renderizar a página
 * 
 * @author	Valdirene da Cruz Neves Júnior <linkinsystem666@gmail.com>
 * @version	2.1
 *
 */
class Template
{
	/**
	 * Guarda o conteúdo da resposta da requisição, pode ser HTML, JSON ou XML
	 * @var	string
	 */
	private $response;
	
	/**
	 * Renderiza a página solicitada pelo usuário
	 * @param	array	$args				argumentos requisitados pelo usuário, como controller, action e parâmetros
	 * @throws	InvalidReturnException		Disparada caso a action solicitada retorne null
	 * @return	void
	 */
	public function render($args)
	{
		$this->master();
		
		$name = controller;
		$controller = new $name();
		$controller->beforeRender();
		
		$content = call_user_func_array(array($controller, action), $args['params']);
		
		if(!$content)
			throw new InvalidReturnException(controller .'->'. action .'()');
		
		$this->renderFlash();
		
		$method = new ReflectionMethod(controller, action);
		$params = $method->getParameters();
		
		for($i = 0; $i < count($params); $i++)
		{
			if(!array_key_exists($params[$i]->getName(), $content->Vars))
				$content->Vars[$params[$i]->getName()] = $args['params'][$i] !== null ? $args['params'][$i] : $params[$i]->getDefaultValue();
		}
		
		if($args['dot'])
		{
			$content->Type = $args['dot'];
			$content->Data = $content->Vars['model'];
		}
		
		switch($content->Type)
		{
			case 'view':
			case 'snippet':
				$this->renderView($content);
				break;
			case 'content':
				$this->renderContent($content);
				break;
			case 'page':
				$this->renderPage($content);
				break;
			case 'xml':
				$this->renderXml($content);
				break;
			case 'json':
				$this->renderJson($content);
				break;
			default:
				throw new InvalidReturnException(controller .'->'. action .'()');
				break;
		}
		$this->response = $controller->afterRender($this->response);
		echo $this->response;
	}
	
	/**
	 * Verifica e retorna o master page deve ser renderizada
	 * @throws	MethodNotFoundException		disparado caso método referente ao nome da master não seja encontrado dentro da MasterController
	 * @throws	MethodVisibilityException	disparado caso método referente ao nome da master não esteja público
	 * @return	string						retorna o nome da master page
	 */
	private function master()
	{
		$annotation = Annotation::get(controller);
		
		if(method_exists('__construct', controller)) 
			$tpl = $annotation->getMethod('__construct')->Master;
		
		if($tpl_action = $annotation->getMethod(action)->Master)
			$tpl = $tpl_action;
		if(!$tpl)
			$tpl = default_master;
		
		define('master', $tpl);
		return $tpl;
	}
	
	/**
	 * Renderiza a flash message
	 * @return	void
	 */
	private function renderFlash()
	{
		$html = '';
		$flash = Session::get('Flash.Message');
		if($flash)
			$html = '<div class="'. $flash->type .'">'. $flash->message .'</div>';
			
		define('flash', $html);
		Session::del('Flash.Message');
	}
	
	/**
	 * Renderiza a view
	 * @param	object	$ob		objeto com informações da view
	 * @return	void
	 */
	private function renderView($ob)
	{
		$html = Import::view($ob->Vars, '_master', master);
		$html = $this->resolveUrl($html);

		$content = Import::view($ob->Vars, $ob->Data['controller'], $ob->Data['view']);
		$content = $this->resolveUrl($content);
		
		$html = str_replace(content, $content, $html);
		$this->response = $html;
	}
	
	/**
	 * Renderiza o conteúdo no lugar da view
	 * @param	object	$ob		objeto com informações do conteúdo
	 * @return	void
	 */
	private function renderContent($ob)
	{
		$html = Import::view($ob->Vars, '_master', master);
		$html = $this->resolveUrl($html);
		
		$content = $ob->Data;
		$content = $this->resolveUrl($content);
		
		$html = str_replace(content, $content, $html);
		$this->response = $html;
	}
	
	/**
	 * Renderiza uma página no lugar da view
	 * @param	object	$ob		objeto com informações da página e da master page
	 * @return	void
	 */
	private function renderPage($ob)
	{
		$this->response = Import::view($ob->Vars, $ob->Data['controller'], $ob->Data['view']);	
	}
	
	/**
	 * Renderiza um conteúdo XML e mata a execução
	 * @param	object	$ob		objeto com informações do XML
	 * @return	void
	 */
	private function renderXml($ob)
	{
		header('Content-type: application/xml; charset='. charset);
		$this->response = '<?xml version="1.0" encoding="'. charset .'"?>';
		$this->response .= xml_encode(d($ob->Data));
	}
	
	/**
	 * Renderiza um conteúdo JSON e mata a execução
	 * @param	object	$ob		objeto com informações do JSON
	 * @return	void
	 */
	private function renderJson($ob)
	{
		header('Content-type: application/json; charset='. charset);
		$this->response = json_encode(utf8encode(d($ob->Data)));
	}
	
	/**
	 * Substitui os '~/' dentro da master e page e da view pelo root virtual
	 * @param	string	$html		HTML da view ou da master page
	 * @return	string				retorna o HTML
	 */
	private function resolveUrl($html)
	{
		return str_replace(array('="~/', "='"), array('="'. root_virtual, "='". root_virtual), $html);
	}
}
