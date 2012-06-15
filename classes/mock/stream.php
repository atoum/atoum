<?php

namespace mageekguy\atoum\mock;

use
	mageekguy\atoum\adapter,
	mageekguy\atoum\exceptions\logic,
	mageekguy\atoum\exceptions\runtime
;

class stream
{
	const defaultProtocol = 'atoum';
	const protocolSeparator = '://';

	public $context = null;

	protected $streamController = null;

	protected static $adapter = null;
	protected static $streams = array();
	protected static $protocols = array();

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
					throw new logic('Argument 0 is not set for function ' . $method . '()');
				}

				$stream = self::slashize($arguments[0]);

				if (isset(self::$streams[$stream]) === false)
				{
					throw new logic('Stream \'' . $arguments[0] . '\' is undefined');
				}

				$this->streamController = self::$streams[$stream];
				break;
		}

		return $this->streamController->invoke($method, $arguments);
	}

	public static function getAdapter()
	{
		return (self::$adapter = self::$adapter ?: new adapter());
	}

	public static function setAdapter(adapter $adapter)
	{
		self::$adapter = $adapter;
	}

	public static function get($stream)
	{
		$stream = self::slashize($stream);

		$adapter = self::getAdapter();

		$protocol = self::getProtocol($stream);

		if ($protocol === null)
		{
			$protocol = self::defaultProtocol;
			$stream = $protocol . self::protocolSeparator . $stream;
		}

		if (in_array($protocol, self::$protocols) === false)
		{
			if (in_array($protocol, $adapter->stream_get_wrappers()) === true)
			{
				throw new runtime('Stream ' . $protocol . ' is already registered');
			}
			else if ($adapter->stream_wrapper_register($protocol, __CLASS__) === false)
			{
				throw new runtime('Unable to register ' . $protocol . ' stream');
			}

			self::$protocols[] = $protocol;
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

		$schemeSeparator = strpos($stream, self::protocolSeparator);

		if ($schemeSeparator !== false)
		{
			$scheme = substr($stream, 0, $schemeSeparator);
		}

		return $scheme;
	}

	public static function slashize($stream)
	{
		$path =  preg_replace('#^[^:]+://#', '', $stream);

		return substr($stream, 0, strlen($stream) - strlen($path)) . str_replace('\\', '/', $path);
	}
}

?>
