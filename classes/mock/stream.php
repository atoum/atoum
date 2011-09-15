<?php

namespace mageekguy\atoum\mock;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class stream
{
	const defaultProtocol = 'atoum';

	public $context = null;

	protected $streamController = null;

	protected static $adapter = null;
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

				if (isset(self::$streams[$arguments[0]]) === false)
				{
					throw new exceptions\logic('Stream \'' . $arguments[0] . '\' is undefined');
				}

				$this->streamController = self::$streams[$arguments[0]];
				break;
		}

		return $this->streamController->invoke($method, $arguments);
	}

	public static function getAdapter()
	{
		self::$adapter = self::$adapter ?: new atoum\adapter();

		return self::$adapter;
	}

	public static function setAdapter(atoum\adapter $adapter)
	{
		self::$adapter = $adapter;
	}

	public static function get($stream)
	{
		$adapter = self::getAdapter();

		$protocol = self::getProtocol($stream);

		if ($protocol === null)
		{
			$protocol = self::defaultProtocol;
			$stream = $protocol . '://' . $stream;
		}

		if (in_array($protocol, $adapter->stream_get_wrappers()) === false && $adapter->stream_wrapper_register($protocol, __CLASS__) === false)
		{
			throw new exceptions\runtime('Unable to register ' . $protocol . ' stream');
		}

		if (isset(self::$streams[$stream]) === false)
		{
			self::$streams[$stream] = new stream\controller();
		}

		return self::$streams[$stream];
	}

	public static function getProtocol($stream)
	{
		$scheme = null;

		$schemeSeparator = strpos($stream, '://');

		if ($schemeSeparator !== false)
		{
			$scheme = substr($stream, 0, $schemeSeparator);
		}

		return $scheme;
	}
}

?>
