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
	public static $streamsSize = array();
	protected static $protocols = array();

	public function __call($method, array $arguments)
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

				$stream = self::setDirectorySeparator($arguments[0]);

				switch ($method)
				{
					case 'unlink':
					case 'rmdir':
						if (isset(self::$streams[$stream]) === false)
						{
							throw new logic('Stream \'' . $arguments[0] . '\' is undefined');
						}

						$this->streamController = self::$streams[$stream];
						$this->streamController->unlink = true;
						$this->streamController->rmdir = true;
						$this->streamController->url_stat = false;

						return $this->setControllerForMethod($method, $arguments)->streamController->invoke($method, $arguments);

					case 'mkdir':
						$directory = pathinfo($stream, PATHINFO_DIRNAME);
						if (preg_match('/:$/', $directory) == false)
						{
							$parent = self::get($directory);
							self::$streams[$stream] = self::getSubStream(
								$parent,
								preg_replace('/^' . preg_quote($directory, '/') . '\//', '', $stream)
							);
							self::$streamsSize[$stream] = 0;

							if (isset(stream::$streamsSize[(string) $parent]) === false)
							{
								stream::$streamsSize[(string) $parent] = 0;
							}

							$parent->dir_readdir[++self::$streamsSize[(string) $parent]] = self::$streams[$stream];
						}
						else
						{
							self::$streams[$stream] = self::get($stream);
						}

						self::$streams[$stream]->dir_opendir = true;
						self::$streams[$stream]->mkdir = true;
						break;

					case 'url_stat':
						if(isset(self::$streams[$stream]) === false && ($arguments[1] & STREAM_URL_STAT_QUIET) == STREAM_URL_STAT_QUIET)
						{
							return false;
						}
						break;
					case 'stream_open':
						switch (true) {
							case preg_match('/a|w|c|x\+?/', $arguments[1]) && isset(static::$streams[$stream]) === false:
								$directory = pathinfo($stream, PATHINFO_DIRNAME);
								if (preg_match('/:$/', $directory) == false)
								{
									$parent = self::get($directory);
									self::$streams[$stream] = static::getSubStream(
										$parent,
										preg_replace('/^' . preg_quote($directory, '/') . '\//', '', $stream)
									);

									if (isset(stream::$streamsSize[(string) $parent]) === false)
									{
										stream::$streamsSize[(string) $parent] = 0;
									}

									$parent->dir_readdir[++static::$streamsSize[(string) $parent]] = static::$streams[$stream];
								}
								else
								{
									static::$streams[$stream] = static::get($stream);
								}

								static::$streams[$stream]->file_get_contents = '';
								break;

							case preg_match('/x\+?/', $arguments[1]) && isset(static::$streams[$stream]):
								throw new logic('Stream \'' . $arguments[0] . '\' already exists');
								break;

							case isset(static::$streams[$stream]) === false:
								throw new logic('Stream \'' . $arguments[0] . '\' is undefined');
								break;
						}

						break;
				}

				if (isset(static::$streams[$stream]) === false)
				{
					throw new logic('Stream \'' . $arguments[0] . '\' is undefined');
				}

				$this->streamController = self::$streams[$stream];
				break;
		}

		return $this->setControllerForMethod($method, $arguments)->streamController->invoke($method, $arguments);
	}

	public function mkdir($path, $mode, $option)
	{
		$stream = self::setDirectorySeparator($path);

		if (isset(self::$streams[$stream]) === true)
		{
			throw new logic('Stream \'' . $path . '\' already exists');
		}

		$directory = pathinfo($stream, PATHINFO_DIRNAME);
		if (preg_match('/:$/', $directory) == false)
		{
			$parent = self::get($directory);
			self::$streams[$stream] = self::getSubStream(
				$parent,
				preg_replace('/^' . preg_quote($directory, '/') . '\//', '', $stream)
			);
			self::$streamsSize[$stream] = 0;

			if (isset(stream::$streamsSize[(string) $parent]) === false)
			{
				stream::$streamsSize[(string) $parent] = 0;
			}

			$parent->dir_readdir[++self::$streamsSize[(string) $parent]] = self::$streams[$stream];
		}
		else
		{
			self::$streams[$stream] = self::get($stream);
		}

		self::$streams[$stream]->dir_opendir = true;
		self::$streams[$stream]->mkdir = true;

		return $this->__call(__FUNCTION__, func_get_args());
	}

	public function rmdir($path, $options)
	{
		$stream = self::setDirectorySeparator($path);

		if (isset(self::$streams[$stream]) === false)
		{
			throw new logic('Stream \'' . $path . '\' is undefined');
		}

		self::$streams[$stream]->rmdir = true;
		self::$streams[$stream]->url_stat = false;
		$streamController = self::$streams[$stream];
		unset(self::$streams[$stream]);

		return $streamController->invoke(__FUNCTION__, func_get_args());
	}

	public function unlink($path)
	{
		$stream = self::setDirectorySeparator($path);

		if (isset(self::$streams[$stream]) === false)
		{
			throw new logic('Stream \'' . $path . '\' is undefined');
		}

		self::$streams[$stream]->unlink = true;
		self::$streams[$stream]->url_stat = false;
		$streamController = self::$streams[$stream];
		unset(self::$streams[$stream]);

		return $streamController->invoke(__FUNCTION__, func_get_args());
	}

	public function url_stat($path, $flags)
	{
		$stream = self::setDirectorySeparator($path);

		if (isset(self::$streams[$stream]) === false && ($flags & STREAM_URL_STAT_QUIET) == STREAM_URL_STAT_QUIET)
		{
			return false;
		}

		return $this->__call(__FUNCTION__, func_get_args());
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
