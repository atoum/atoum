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

	protected $name = '';
	protected $factory = null;
	protected $locale = null;
	protected $adapter = null;
	protected $outputWriter = null;
	protected $errorWriter = null;

	private $help = array();
	private $argumentsParser = null;

	public function __construct($name, atoum\factory $factory = null)
	{
		$this->name = (string) $name;

		$this
			->setFactory($factory ?: new atoum\factory())
			->setLocale($this->factory['atoum\locale']())
			->setAdapter($this->factory['atoum\adapter']())
			->setArgumentsParser($this->factory['atoum\script\arguments\parser']())
			->setOutputWriter($this->factory['atoum\writers\std\out']())
			->setErrorWriter($this->factory['atoum\writers\std\err']())
		;

		$this->factory['atoum\locale'] = $this->locale;
		$this->factory['atoum\adapter'] = $this->adapter;

		if ($this->adapter->php_sapi_name() !== 'cli')
		{
			throw new exceptions\logic('\'' . $this->getName() . '\' must be used in CLI only');
	 	}
	}

	public function setFactory(atoum\factory $factory)
	{
		$this->factory = $factory->import('mageekguy\atoum');

		return $this;
	}

	public function getFactory()
	{
		return $this->factory;
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
		$this->argumentsParser = $parser;

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

	public function getLocale()
	{
		return $this->locale;
	}

	public function getHelp()
	{
		return $this->help;
	}

	public function setLocale(atoum\locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function help()
	{
		if ($this->help)
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

	public function addArgumentHandler(\closure $handler, array $arguments, $values = null, $help = null, $priority = 0)
	{
		if ($help !== null)
		{
			$this->help[] = array($arguments, $values, $help);
		}

		$this->argumentsParser->addHandler($handler, $arguments, $priority);

		return $this;
	}

	public function run(array $arguments = array())
	{
		$this->adapter->ini_set('log_errors_max_len', '0');
		$this->adapter->ini_set('log_errors', 'Off');
		$this->adapter->ini_set('display_errors', 'stderr');

		$this->argumentsParser->parse($this, $arguments);

		return $this;
	}

	public function prompt($message)
	{
		$this->outputWriter->write(rtrim($message));

		return trim($this->adapter->fgets(STDIN));
	}

	public function writeMessage($message, $eol = true)
	{
		$message = rtrim($message);

		if ($eol == true)
		{
			$message .= PHP_EOL;
		}

		$this->outputWriter->write($message);

		return $this;
	}

	public function writeError($message)
	{
		$this->errorWriter->write(sprintf($this->locale->_('Error: %s'), trim($message)) . PHP_EOL);

		return $this;
	}

	public function clearMessage()
	{
		$this->outputWriter->clear();

		return $this;
	}

	public function writeLabel($label, $value, $level = 0)
	{
		return $this->writeMessage(($level <= 0 ? '' : str_repeat(self::padding, $level)) . (preg_match('/^ +$/', $label) ? $label : rtrim($label)) . ': ' . trim($value) . PHP_EOL);
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

	protected function setArgumentHandlers()
	{
		$this->help = array();

		return $this;
	}
}
