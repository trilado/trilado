<?php

/*
 * Copyright (c) 2012-2013, Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * All rights reserved.
 */

/**
 * Classe de manipulação e apresentação dos erros
 * @author	Valdirene da Cruz Neves Júnior <vaneves@vaneves.com>
 * @version	1.4
 * 
 */
class Error
{

	/**
	 * Método que é executado quando ocorre algum erro no PHP
	 * @param	int		$type		tipo do erro, que pode ser E_STRICT
	 * @param	sintrg	$message	mensagem do erro
	 * @param	string	$file		endereço completo do arquivo que ocorreu o erro
	 * @param	int		$line		linha do arquivo em que ocorreu o erro
	 * @return	void
	 */
	public static function handle($type, $message, $file, $line)
	{
		ob_get_level() and ob_clean();

		if (Debug::enabled())
		{
			if ($type != E_STRICT)
			{
				$details = self::lineAsString($file, $line);
				$trace = self::traceAsString();

				self::render(500, $message, $file, $line, $trace, $details);

				exit;
			}
		}
		else
		{
			$types = array(
				E_ERROR => 'ERROR',
				E_WARNING => 'WARNING',
				E_PARSE => 'PARSING ERROR',
				E_NOTICE => 'NOTICE',
				E_CORE_ERROR => 'CORE ERROR',
				E_CORE_WARNING => 'CORE WARNING',
				E_COMPILE_ERROR => 'COMPILE ERROR',
				E_COMPILE_WARNING => 'COMPILE WARNING',
				E_USER_ERROR => 'USER ERROR',
				E_USER_WARNING => 'USER WARNING',
				E_USER_NOTICE => 'USER NOTICE',
				E_STRICT => 'RUNTIME NOTICE'
			);
			$t = 'CAUGHT ERROR';
			if (isset($types[$type]))
				$t = $types[$type];

			$log = date('Y-m-d H:i:s') . ' ' . $t . ': ' . $message . ' in ' . $file . ' (' . $line . ')' . "\r\n";
			self::log($log);
		}
	}

	/**
	 * Método executado quando ocorre algum erro fatal no PHP, esse método é chamado 
	 * antes que o PHP pare a execução da página
	 * @return	void
	 */
	public static function shutdown()
	{
		$error = error_get_last();
		if (is_array($error))
			self::handle($error['type'], $error['message'], $error['file'], $error['line']);
	}

	/**
	 * Método executado quando algumas exceção não foi tratada 
	 */
	public static function exception()
	{
		
	}

	/**
	 * Método para apresentar a página de erro. Mata execução
	 * @param	int		$number		número do erro HTTP
	 * @param	string	$message	mensagem do erro
	 * @param	string	$file		endereço completo do arquivo em que ocorreu o erro
	 * @param	int		$line		número da linha em que ocorreu o erro
	 * @param	string	$trace		trilha de arquivo antes de ocorrer o erro
	 * @param	string	$details	detalhes do erro, apresenta o trecho do arquivo em que ocorreu o erro
	 * @return	void
	 */
	public static function render($number, $message, $file, $line, $trace = null, $details = null)
	{
		$error_controller = Config::get('error_controller');
		if (Debug::enabled())
		{
			return require_once root . 'core/error/debug.php';
		}
		elseif ($error_controller && file_exists(ROOT . 'app/controllers/' . $error_controller . 'Controller.php') && is_subclass_of($error_controller . 'Controller', 'Controller'))
		{
			$error_controller .= 'Controller';
			$actions = array('error' . $number, 'index');
			$i = NULL;

			foreach ($actions as $k => $a)
			{
				if (method_exists($error_controller, $a))
				{
					$method = new ReflectionMethod($error_controller, $a);
					if ($method->isPublic() || !$method->isStatic() || $this->isValidParams($method))
					{
						$i = $k;
						break;
					}
				}
			}

			if ($i !== NULL)
			{
				App::$controller = $error_controller;
				App::$action = $actions[$i];

				$tpl = new Template();

				$registry = Registry::getInstance();
				$registry->set('Template', $tpl);

				$tpl->render(array(
					'params' => array(
						'number' => $number,
						'message' => $message,
						'file' => $file,
						'line' => $line,
						'trace' => $trace,
						'details' => $details
					)
				));

				return;
			}
		}

		self::defaultRender($number, $message, $file, $line, $trace, $details);
	}

	/**
	 * Método padrão para apresentar a página de erro caso não haja um controller para isso. Mata execução
	 * @param	int		$number		número do erro HTTP
	 * @param	string	$message	mensagem do erro
	 * @param	string	$file		endereço completo do arquivo em que ocorreu o erro
	 * @param	int		$line		número da linha em que ocorreu o erro
	 * @param	string	$trace		trilha de arquivo antes de ocorrer o erro
	 * @param	string	$details	detalhes do erro, apresenta o trecho do arquivo em que ocorreu o erro
	 * @return	void
	 */
	private static function defaultRender($number, $message, $file, $line, $trace = null, $details = null)
	{
		$files = array();
		$files[0] = root . 'app/views/_error/' . $number . '.php';
		$files[1] = root . 'core/error/default.php';
		foreach ($files as $f)
		{
			if (file_exists($f))
				return require_once $f;
		}
		exit('error');
	}

	/**
	 * Monta a trilha do erro, retornando um array com os nomes do arquivos, classes, métodos e linhas
	 * @param	array	$trace	trilha padrão para montar a nova trilha
	 * @return	array	 retornando um array com os nomes do arquivos, classes, métodos e linhas
	 */
	public static function trace($trace = null)
	{
		if ($trace == null)
			$trace = debug_backtrace();

		$output = array();
		$i = 0;
		foreach ($trace as $v)
		{
			if (!isset($v['function']))
				continue;

			$function = $v['function'];
			if (isset($v['class']))
				$function = $v['class'] . $v['type'] . $v['function'];

			$output[$i]['function'] = $function;
			$output[$i]['args'] = isset($v['args']) ? $v['args'] : '';
			$output[$i]['file'] = isset($v['file']) ? $v['file'] : '';
			$output[$i]['line'] = isset($v['line']) ? $v['line'] : '';
			$output[$i]['source'] = isset($v['source']) ? $v['source'] : '';

			++$i;
		}
		return $output;
	}

	/**
	 * Transforma uma trilha em uma string para impressão
	 * @param	array	$trace	trilha padrão, se for informado, é chamado o método trace
	 * @return	string	retorna a trinha em formato para impressão 
	 */
	public static function traceAsString($trace = null)
	{
		if ($trace == null)
			$trace = self::trace();

		$output = '';
		foreach ($trace as $k => $v)
			$output .= '#' . $k . ' ' . $v['file'] . '(' . $v['line'] . ') ' . $v['function'] . "\r\n";

		return $output;
	}

	/**
	 * Captura o trecho do arquivo de acordo com a linha passado como parâmetro e retorna as linhas como array
	 * @param	string	$file		endereço completo do arquivo em que ocorreu o erro
	 * @param	int		$line		número da linha em que ocorreu o erro
	 * @param	int		$padding	quantidade de linha antes e depois da linha do erro serão capturadas
	 * @return	array	retorna um array contendo os trechos das linhas capturadas
	 */
	public static function line($file, $line, $padding = 5)
	{
		$output = array();
		$i = 1;
		$handle = fopen($file, "r");
		if ($handle)
		{
			$start = $line - $padding;
			$end = $line + $padding;

			while ($i <= $end && (($buffer = fgets($handle, 4096)) !== false))
			{
				if ($i >= $start)
					$output[$i] = $buffer;
				++$i;
			}
			fclose($handle);
		}
		return $output;
	}

	/**
	 * Captura o trecho do arquivo de acordo com a linha passado como parâmetro e retorna as linhas como HTML
	 * @param	string	$file		endereço completo do arquivo em que ocorreu o erro
	 * @param	int		$line		número da linha em que ocorreu o erro
	 * @param	int		$padding	quantidade de linha antes e depois da linha do erro serão capturadas
	 * @return	array	retorna um HTML contendo os trechos das linhas capturadas
	 */
	public static function lineAsString($file, $line, $padding = 5)
	{
		$lines = self::line($file, $line, $padding);

		$output = '';
		foreach ($lines as $i => $l)
		{
			if ($i == $line)
				$output .= '<span class="line line-error"><span class="line-n">' . $i . '</span>' . htmlentities($l) . '</span>';
			else
				$output .= '<span class="line"><span class="line-n">' . $i . '</span>' . htmlentities($l) . '</span>';
		}
		return $output;
	}

	/**
	 * Escreve uma mensagem nos arquivos de logs
	 * @param	string	$string		conteúdo a ser escrito
	 * @return	boolean				retorna true caso o log seja gerado, no contrário retorna false
	 */
	public static function log($string)
	{
		$bytes = file_put_contents(ROOT . 'app/tmp/logs/' . date('Y-m-d') . '.log', $string, FILE_APPEND);
		return $bytes !== false;
	}

}
