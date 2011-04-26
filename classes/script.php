<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;
use \mageekguy\atoum\script;
use \mageekguy\atoum\exceptions;

abstract class script implements atoum\adapter\aggregator
{
	const padding = '   ';

	protected $locale = null;
	protected $outputWriter = null;
	protected $errorWriter = null;
	protected $argumentsParser = null;
	protected $name = '';

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		$this->name = (string) $name;

		if ($locale === null)
		{
			$locale = new atoum\locale();
		}

		$this->setLocale($locale);

		if ($adapter === null)
		{
			$adapter = new atoum\adapter();
		}

		$this->setAdapter($adapter);

		if (isset($this->adapter->exit) === false)
		{
			$this->adapter->exit = function($code) { exit($code); };
		}

		if ($this->adapter->php_sapi_name() !== 'cli')
		{
			throw new exceptions\logic('\'' . $this->getName() . '\' must be used in CLI only');
		}

		$this
			->setArgumentsParser(new script\arguments\parser())
			->setOutputWriter(new atoum\writers\std\out())
			->setErrorWriter(new atoum\writers\std\err())
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

	public function setArgumentsParser(script\arguments\parser $parser)
	{
		$this->argumentsParser = $parser->setScript($this);

		return $this;
	}

	public function setAdapter(atoum\adapter $adapter)
	{
		$this->adapter = $adapter;

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

	public function getArgumentsParser()
	{
		return $this->argumentsParser;
	}

	public function setLocale(atoum\locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function run(array $arguments = array())
	{
		$this->argumentsParser->parse(sizeof($arguments) <= 0 ? null : $arguments);

		return $this;
	}

	public function writeMessage($message)
	{
		$this->outputWriter->write(rtrim($message) . PHP_EOL);

		return $this;
	}

	public function writeError($message)
	{
		$this->errorWriter->write(sprintf($this->locale->_('Error: %s'), rtrim($message)) . PHP_EOL);

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
}

?>
