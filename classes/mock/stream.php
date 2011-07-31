<?php

namespace mageekguy\atoum\mock;

use
	mageekguy\atoum\exceptions
;

class stream
{
	const name = 'atoum';

	protected static $streams = array();

	public function __call($method, $arguments)
	{
		return $this->invoke($method, $arguments);
	}

	public static function create($stream)
	{
		eval(self::generateClass($stream));

		if (stream_wrapper_register($stream, __NAMESPACE__ . '\streams\\' . $stream) === false)
		{
			throw new exceptions\runtime('Unable to register ' . self::name . ' stream');
		}

		return self::get($stream);
	}

	public static function get($stream)
	{
		if (isset(self::$streams[$stream]) === false)
		{
			self::$streams[$stream] = new stream\controller();
		}

		return self::$streams[$stream];
	}

	protected static function generateClass($stream)
	{
		return
			'namespace ' . __NAMESPACE__ . '\streams;' . PHP_EOL .
			'{' . PHP_EOL .
			"\t" . 'final class ' . $stream . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'public function __construct()' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . 'return \mageekguy\atoum\mock\stream::get(\'' . $stream . '\')->invoke(\'__construct\');' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'public function __call($method, $arguments)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . 'return \mageekguy\atoum\mock\stream::get(\'' . $stream . '\')->invoke($method, $arguments);' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t" . '}' .
			'}'
		;
	}
}

?>
