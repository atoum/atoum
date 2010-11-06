<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

abstract class script
{
	const padding = '   ';

	protected $locale = null;
	protected $arguments = array();
	protected $outputWriter = null;
	protected $errorWriter = null;

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

		$this
			->setOutputWriter(new atoum\writers\stdout())
			->setErrorWriter(new atoum\writers\stderr())
			->locale = $locale
		;
	}

	public function setOutputWriter(atoum\writer $writer)
	{
		$this->outputWriter = $writer;

		return $this;
	}

	public function setErrorWriter(atoum\writer $writer)
	{
		$this->errorWriter = $writer;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function getOutputWriter()
	{
		return $this->outputWriter;
	}

	public function getErrorWriter()
	{
		return $this->errorWriter;
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

	public function run(atoum\superglobal $superglobal = null)
	{
		if ($superglobal === null)
		{
			$superglobal = new atoum\superglobal();
		}

		$this->arguments = new \arrayIterator(array_slice($superglobal->_SERVER['argv'], 1));

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

	public function writeMessage($message)
	{
		$this->outputWriter->write($message);

		return $this;
	}

	public function writeError($message)
	{
		$this->errorWriter->write(sprintf($this->locale->_('Error: %s'), $message));

		return $this;
	}

	public function writeLabel($label, $value, $level = 0)
	{
		return $this->writeMessage(($level <= 0 ? '' : str_repeat(self::padding, $level)) . $label . ': ' . $value . PHP_EOL);
	}

	public function writeLabels(array $labels, $level = 1)
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

			$this->writeLabel(str_pad($label, $maxLength, ' ', STR_PAD_LEFT), $value[0], $level);

			if (sizeof($value) > 1)
			{
				foreach (array_slice($value, 1) as $line)
				{
					$this->writeLabel(str_repeat(' ', $maxLength), $line, $level);
				}
			}
		}

		return $this;
	}

	protected abstract function handleArgument($argument);

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
}

?>
