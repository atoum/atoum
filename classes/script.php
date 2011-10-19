<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\script,
	mageekguy\atoum\exceptions
;

abstract class script implements atoum\adapter\aggregator
{
	const padding = '   ';

	protected $locale = null;
	protected $outputWriter = null;
	protected $errorWriter = null;
	protected $name = '';

	private $help = array();
	private $argumentsParser = null;

	public function __construct($name, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		$this->name = (string) $name;

		$this
			->setLocale($locale ?: new atoum\locale())
			->setAdapter($adapter ?: new atoum\adapter())
			->setArgumentsParser(new script\arguments\parser())
			->setOutputWriter(new atoum\writers\std\out())
			->setErrorWriter(new atoum\writers\std\err())
		;

		if (isset($this->adapter->exit) === false)
		{
			$this->adapter->exit = function($code) { exit($code); };
		}

		if ($this->adapter->php_sapi_name() !== 'cli')
		{
			throw new exceptions\logic('\'' . $this->getName() . '\' must be used in CLI only');
		}
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

		$this->setArgumentHandlers();

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

	public function getHelp()
	{
		return $this->help;
	}

	public function help()
	{
		if (sizeof($this->help) > 0)
		{
			$this
				->writeMessage(sprintf($this->locale->_('Usage: %s [options]'), $this->getName()) . PHP_EOL)
				->writeMessage($this->locale->_('Available options are:') . PHP_EOL)
			;

			$arguments = array();

			foreach ($this->help as $help)
			{
				if ($help[1] !== null)
				{
					foreach ($help[0] as & $argument)
					{
						$argument .= ' ' . $help[1];
					}
				}

				$arguments[join(', ', $help[0])] = $help[2];
			}

			$this->writeLabels($arguments);
		}

		return $this;
	}

	public function run(array $arguments = array())
	{
		ini_set('log_errors_max_len', '0');
		ini_set('log_errors', 'Off');
		ini_set('display_errors', 'stderr');

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

	protected function addArgumentHandler(\closure $handler, array $arguments, $values = null, $help = null)
	{
		if ($help !== null)
		{
			$this->help[] = array($arguments, $values, $help);
		}

		$this->argumentsParser->addHandler($handler, $arguments);

		return $this;
	}

	protected abstract function setArgumentHandlers();
}

?>
