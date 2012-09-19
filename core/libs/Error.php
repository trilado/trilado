<?php

class Error
{	
	public static function handle($type, $message, $file, $line)
	{
		ob_get_level() and ob_clean();
		
		if(Debug::enabled())
		{
			if($type != E_STRICT)
			{
				$details = self::lineAsString($file, $line);
				$trace = self::traceAsString();

				self::render(500, $message, $file, $line, $trace, $details);

				exit;
			}
		}
	}

	public static function shutdown()
	{
		$error = error_get_last();
		if (is_array($error))
			self::handle($error['type'], $error['message'], $error['file'], $error['line']);
	}

	public static function exception()
	{
		
	}

	public static function render($number, $message, $file, $line, $trace = null, $details = null)
	{
		if (Debug::enabled())
			return require_once root . 'core/error/debug.php';

		$files = array();
		$files[0] = root . 'app/views/_error/' . $number . '.php';
		$files[1] = root . 'core/error/' . $number . '.php';
		foreach ($files as $f)
		{
			if (file_exists($f))
				return require_once $f;
		}
		exit('error');
	}

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

	public static function traceAsString($trace = null)
	{
		if ($trace == null)
			$trace = self::trace();

		$output = '';
		foreach ($trace as $k => $v)
			$output .= '#' . $k . ' ' . $v['file'] . '(' . $v['line'] . ') ' . $v['function'] . nl;

		return $output;
	}

	public static function line($file, $line, $padding = 5)
	{
		$output = array();
		$i = 1;
		$handle = fopen($file, "r");
		if ($handle)
		{
			$start = $line - $padding;
			$end = $line + $padding;
			
			$finish = false;
			
			while ($finish == false && (($buffer = fgets($handle, 4096)) !== false))
			{
				if($i == $end)
				{
					$finish = true;
				}
				else
				{
					if($line >= $start)
						$output[$i] = $buffer;
				}
				++$i;
			}
			if (!feof($handle))
				echo "Error: unexpected fgets() fail\n";
			fclose($handle);
		}
		return $output;
	}

	public static function lineAsString($file, $line, $padding = 5)
	{
		$lines = self::line($file, $line, $padding);
		
		$output = '';
		foreach($lines as $i => $l)
		{
			if($i == $line)
				$output .= '<span class="line line-error"><span class="line-n">'. $i .'</span>' . htmlentities($l) . '</span>';
			else
				$output .= '<span class="line"><span class="line-n">'. $i .'</span>' . htmlentities($l) . '</span>';
		}
		return $output;
	}
}
