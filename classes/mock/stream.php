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

				$stream = static::setDirectorySeparator($arguments[0]);

				if (isset(static::$streams[$stream]) === false)
				{
					throw new logic('Stream \'' . $arguments[0] . '\' is undefined');
				}

				$this->streamController = static::$streams[$stream];
				break;
		}

		return $this->streamController->invoke($method, $arguments);
	}

	public static function getAdapter()
	{
		return (static::$adapter = static::$adapter ?: new adapter());
	}

	public static function setAdapter(adapter $adapter)
	{
		static::$adapter = $adapter;
	}

	public static function get($stream = null)
	{
		$stream = static::setDirectorySeparator($stream ?: uniqid());

		$adapter = static::getAdapter();

		if (($protocol = static::getProtocol($stream)) === null)
		{
			$protocol = static::defaultProtocol;
			$stream = $protocol . static::protocolSeparator . $stream;
		}

		if (in_array($protocol, $adapter->stream_get_wrappers()) === false && $adapter->stream_wrapper_register($protocol, __CLASS__) === false)
		{
			throw new runtime('Unable to register ' . $protocol . ' stream');
		}

		if (isset(static::$streams[$stream]) === false)
		{
			static::$streams[$stream] = new stream\controller($stream);
		}

		return static::$streams[$stream];
	}

	public static function getSubStream(stream\controller $controller, $stream = null)
	{
		return static::get($controller . DIRECTORY_SEPARATOR . static::setDirectorySeparator($stream ?: uniqid()));
	}

	public static function getProtocol($stream)
	{
		$scheme = null;

		$schemeSeparator = strpos($stream, static::protocolSeparator);

		if ($schemeSeparator !== false)
		{
			$scheme = substr($stream, 0, $schemeSeparator);
		}

		return $scheme;
	}

	public static function setDirectorySeparator($stream, $directorySeparator = DIRECTORY_SEPARATOR)
	{
		$path =  preg_replace('#^[^:]+://#', '', $stream);

		if ($directorySeparator == '/')
		{
			$path = str_replace('\\', '/', $path);
		}
		else
		{
			$path = str_replace('/', '\\', $path);
		}

		return substr($stream, 0, strlen($stream) - strlen($path)) . $path;
	}
}
