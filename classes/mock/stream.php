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

	public function __call($method, array $arguments)
	{
		return $this->setControllerForMethod($method, $arguments)->streamController->invoke($method, $arguments);
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

		if (in_array($protocol, $adapter->stream_get_wrappers()) === false && $adapter->stream_wrapper_register($protocol, get_called_class(), 0) === false)
		{
			throw new runtime('Unable to register ' . $protocol . ' stream');
		}

		if (isset(static::$streams[$stream]) === false)
		{
			static::$streams[$stream] = static::getController($stream);
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

	protected function setControllerForMethod($method, array $arguments)
	{
		switch (strtolower($method))
		{
			case 'dir_opendir':
			case 'mkdir':
			case 'rename':
			case 'rmdir':
			case 'stream_metadata':
			case 'stream_open':
			case 'unlink':
			case 'url_stat':
			case 'stat':
				$streamController = static::getStreamFromArguments($arguments);
				$this->streamController = clone $streamController;
				$this->streamController->linkCallsTo($streamController);
				break;
		}

		return $this;
	}

	protected static function getStreamFromArguments(array $arguments)
	{
		if (isset($arguments[0]) === false)
		{
			throw new logic('Argument 0 is undefined for function ' . $method . '()');
		}

		$stream = static::setDirectorySeparator($arguments[0]);

		if (isset(static::$streams[$stream]) === false)
		{
			throw new logic('Stream \'' . $arguments[0] . '\' is undefined');
		}

		return static::$streams[$stream];
	}

	protected static function getController($stream)
	{
		return new stream\controller($stream);
	}
}
