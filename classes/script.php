<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

abstract class script
{
	const padding = '   ';

	protected $locale = null;
	protected $arguments = array();

	private $name = '';

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		if ($adapter === null)
		{
			$adapter = new atoum\adapter();
		}

		$this->adapter = $adapter;

		if (isset($this->adapter->exit) === false)
		{
			$adapter->exit = function($code) { exit($code); };
		}

		if ($this->adapter->php_sapi_name() !== 'cli')
		{
			throw new \logicException('\'' . $name . '\' must be used in CLI only');
		}

		$this->name = $name;

		if ($locale === null)
		{
			$locale = new atoum\locale();
		}

		$this->locale = $locale;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function run()
	{
		$this->adapter->set_error_handler(array($this, 'errorHandler'));
		$this->adapter->set_exception_handler(array($this, 'exceptionHandler'));

		$this->arguments = new \arrayIterator(array_slice($_SERVER['argv'], 1));

		foreach ($this->arguments as $argument)
		{
			if (self::isArgument($argument) === false)
			{
				throw new \logicException('Argument \'' . $argument . '\' is invalid');
			}

			$this->handleArgument($argument);
		}

		return $this;
	}

	public function errorHandler($error, $message, $file, $line)
	{
		$this->stop($error, $message);
	}

	public function exceptionHandler(\exception $exception)
	{
		$this->stop($exception->getCode(), $exception->getMessage());
	}

	protected abstract function handleArgument($argument);

	protected static function writeMessage($message)
	{
		fwrite(STDOUT, rtrim($message) . "\n");
	}

	protected static function writeLabel($label, $value, $level = 0)
	{
		self::writeMessage(($level <= 0 ? '' : str_repeat(self::padding, $level)) . $label . ': ' . $value);
	}

	protected static function writeLabels(array $labels, $level = 1)
	{
		$maxLength = 0;

		foreach (array_keys($labels) as $label)
		{
			$length = strlen($label);

			if ($length > $maxLength)
			{
				$maxLength = $length;
			}
		}

		foreach ($labels as $label => $value)
		{
			$value = explode("\n", trim($value));

			self::writeLabel(str_pad($label, $maxLength, ' ', STR_PAD_LEFT), $value[0], $level);

			if (sizeof($value) > 1)
			{
				foreach (array_slice($value, 1) as $line)
				{
					self::writeLabel(str_repeat(' ', $maxLength), $line, $level);
				}
			}
		}
	}

	protected static function isArgument($string)
	{
		switch (substr($string, 0, 1))
		{
			case '+':
			case '-':
			case '--':
				return true;

			default:
				return false;
		}
	}

	protected function stop($code, $message)
	{
		fwrite(STDERR, sprintf($this->locale->_('Error: %s.'), rtrim(rtrim($message, '.'))) . "\n");
		$this->adapter->exit($code);
	}
}

?>
