<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

abstract class script
{
	const padding = '   ';

	protected $locale = null;
	protected $arguments = null;

	private $name = '';
	private $errors = array();

	public function __construct($name, locale $locale = null)
	{
		if (PHP_SAPI !== 'cli')
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

	public function getName()
	{
		return $this->name;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function run()
	{
		set_error_handler(array(__CLASS__, 'errorHandler'));
		set_exception_handler(array(__CLASS__, 'exceptionHandler'));

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

	public static function errorHandler($error, $message, $file, $line)
	{
		self::stop($error, $message);
	}

	public static function exceptionHandler(\exception $exception)
	{
		self::stop($exception->getCode(), $exception->getMessage());
	}

	protected function addError($error)
	{
		$this->errors[] = $error;
		return $this;
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

	protected static function writeError($message)
	{
		fwrite(STDERR, rtrim($message) . "\n");
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

	protected static function stop($code, $message)
	{
		self::writeError($message);
		die($code);
	}
}

?>
