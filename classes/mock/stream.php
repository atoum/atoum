<?php

namespace mageekguy\atoum\mock;

use
	mageekguy\atoum\exceptions
;

class stream
{
	const name = 'atoum';

	public $context = null;

	protected $streamController = null;

	protected static $streams = array();

	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'dir_opendir':
			case 'mkdir':
			case 'rename':
			case 'rmdir':
			case 'stream_metadata':
			case 'stream_open':
			case 'unlink':
			case 'url_stat':
				if (isset($arguments[0]) === false)
				{
					throw new exceptions\logic('Argument 0 is not set for function ' . $method . '()');
				}

				$scheme = self::name . '://';

				if (strpos($arguments[0], $scheme) !== 0)
				{
					throw new exceptions\logic('Scheme is invalid in \'' . $argument[0] . '\'');
				}

				$name = substr($arguments[0], strlen($scheme));

				if (isset(self::$streams[$name]) === false)
				{
					throw new exceptions\logic('Stream \'' . $argument[0] . '\' is undefined');
				}

				$this->streamController = self::$streams[$name];
				break;
		}

		return $this->streamController->invoke($method, $arguments);
	}

	public static function get($stream)
	{
		if (in_array(self::name, stream_get_wrappers()) === false && stream_wrapper_register(self::name, __CLASS__) === false)
		{
			throw new exceptions\runtime('Unable to register ' . self::name . ' stream');
		}

		if (isset(self::$streams[$stream]) === false)
		{
			self::$streams[$stream] = new stream\controller();
		}

		return self::$streams[$stream];
	}
}

?>
