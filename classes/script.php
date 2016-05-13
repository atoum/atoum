<?php

namespace mageekguy\atoum;

abstract class script
{
	const padding = '   ';

	protected $name = '';
	protected $locale = null;
	protected $adapter = null;
	protected $prompt = null;
	protected $cli = null;
	protected $verbosityLevel = 0;
	protected $outputWriter = null;
	protected $infoWriter = null;
	protected $warningWriter = null;
	protected $errorWriter = null;
	protected $helpWriter = null;
	protected $argumentsParser = null;

	private $doRun = true;
	private $help = array();

	public function __construct($name, adapter $adapter = null)
	{
		$this->name = (string) $name;

		$this
			->setCli()
			->setAdapter($adapter)
			->setLocale()
			->setPrompt()
			->setArgumentsParser()
			->setOutputWriter()
			->setInfoWriter()
			->setErrorWriter()
			->setWarningWriter()
			->setHelpWriter()
		;

		if ($this->adapter->php_sapi_name() !== 'cli')
		{
			throw new exceptions\logic('\'' . $this->getName() . '\' must be used in CLI only');
	 	}
	}

	public function getDirectory()
	{
		$directory = $this->adapter->dirname($this->getName());

		if ($this->adapter->is_dir($directory) === false)
		{
			$directory = $this->adapter->getcwd();
		}

		return rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	public function setAdapter(adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setLocale(locale $locale = null)
	{
		$this->locale = $locale ?: new locale();

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setArgumentsParser(script\arguments\parser $parser = null)
	{
		$this->argumentsParser = $parser ?: new script\arguments\parser();

		$this->setArgumentHandlers();

		return $this;
	}

	public function getArgumentsParser()
	{
		return $this->argumentsParser;
	}

	public function setCli(cli $cli = null)
	{
		$this->cli = $cli ?: new cli();

		return $this;
	}

	public function getCli()
	{
		return $this->cli;
	}

	public function hasArguments()
	{
		return (sizeof($this->argumentsParser->getValues()) > 0);
	}

	public function setOutputWriter(writer $writer = null)
	{
		$this->outputWriter = $writer ?: new writers\std\out($this->cli);

		return $this;
	}

	public function getOutputWriter()
	{
		return $this->outputWriter;
	}

	public function setInfoWriter(writer $writer = null)
	{
		if ($writer === null)
		{
			$writer = new writers\std\out($this->cli);
			$writer
				->addDecorator(new writer\decorators\rtrim())
				->addDecorator(new writer\decorators\eol())
				->addDecorator(new cli\clear())
			;
		}

		$this->infoWriter = $writer;

		return $this;
	}

	public function getInfoWriter()
	{
		return $this->infoWriter;
	}

	public function setWarningWriter(writer $writer = null)
	{
		if ($writer === null)
		{
			$writer = new writers\std\err($this->cli);
			$writer
				->addDecorator(new writer\decorators\trim())
				->addDecorator(new writer\decorators\prompt($this->locale->_('Warning: ')))
				->addDecorator(new writer\decorators\eol())
				->addDecorator(new cli\clear())
			;
		}

		$this->warningWriter = $writer;

		return $this;
	}

	public function getWarningWriter()
	{
		return $this->warningWriter;
	}

	public function setErrorWriter(writer $writer = null)
	{
		if ($writer === null)
		{
			$writer = new writers\std\err($this->cli);
			$writer
				->addDecorator(new writer\decorators\trim())
				->addDecorator(new writer\decorators\prompt($this->locale->_('Error: ')))
				->addDecorator(new writer\decorators\eol())
				->addDecorator(new cli\clear())
			;
		}

		$this->errorWriter = $writer;

		return $this;
	}

	public function getErrorWriter()
	{
		return $this->errorWriter;
	}

	public function setHelpWriter(writer $writer = null)
	{
		if ($writer === null)
		{
			$labelColorizer = new cli\colorizer('0;32');
			$labelColorizer->setPattern('/(^[^:]+: )/');

			$argumentColorizer = new cli\colorizer('0;32');
			$argumentColorizer->setPattern('/((?:^| )[-+]+[-a-z]+)/');

			$valueColorizer = new cli\colorizer('0;34');
			$valueColorizer->setPattern('/(<[^>]+>(?:\.\.\.)?)/');

			$writer = new writers\std\out();
			$writer
				->addDecorator($labelColorizer)
				->addDecorator($valueColorizer)
				->addDecorator($argumentColorizer)
				->addDecorator(new writer\decorators\rtrim())
				->addDecorator(new writer\decorators\eol())
				->addDecorator(new cli\clear())
			;
		}

		$this->helpWriter = $writer;

		return $this;
	}

	public function getHelpWriter()
	{
		return $this->helpWriter;
	}

	public function setPrompt(script\prompt $prompt = null)
	{
		if ($prompt === null)
		{
			$prompt = new script\prompt();
		}

		$this->prompt = $prompt->setOutputWriter($this->outputWriter);

		return $this;
	}

	public function getPrompt()
	{
		return $this->prompt;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getHelp()
	{
		return $this->help;
	}

	public function help()
	{
		return $this
			->writeHelpUsage()
			->writeHelpOptions()
			->stopRun()
		;
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

	public function setDefaultArgumentHandler(\closure $handler)
	{
		$this->argumentsParser->setDefaultHandler($handler);

		return $this;
	}

	public function run(array $arguments = array())
	{
		$this->adapter->ini_set('log_errors_max_len', 0);
		$this->adapter->ini_set('log_errors', 'Off');
		$this->adapter->ini_set('display_errors', 'stderr');

		$this->doRun = true;

		if ($this->parseArguments($arguments)->doRun === true)
		{
			$this->doRun();
		}

		return $this;
	}

	public function prompt($message)
	{
		return trim($this->prompt->ask(rtrim($message)));
	}

	public function writeLabel($label, $value, $level = 0)
	{
		static::writeLabelWithWriter($label, $value, $level, $this->helpWriter);

		return $this;
	}

	public function writeLabels(array $labels, $level = 1)
	{
		static::writeLabelsWithWriter($labels, $level, $this->helpWriter);

		return $this;
	}

	public function clearMessage()
	{
		$this->outputWriter->clear();

		return $this;
	}

	public function writeMessage($message)
	{
		$this->outputWriter
			->removeDecorators()
			->write($message)
		;

		return $this;
	}

	public function writeInfo($info)
	{
		$this->infoWriter->write($info);

		return $this;
	}

	public function writeHelp($message)
	{
		$this->helpWriter->write($message);

		return $this;
	}

	public function writeWarning($warning)
	{
		$this->warningWriter->write($warning);

		return $this;
	}

	public function writeError($message)
	{
		$this->errorWriter->write($message);

		return $this;
	}

	public function verbose($message, $verbosityLevel = 1)
	{
		if ($verbosityLevel > 0 && $this->verbosityLevel >= $verbosityLevel)
		{
			$this->writeInfo($message);
		}

		return $this;
	}

	public function increaseVerbosityLevel()
	{
		$this->verbosityLevel++;

		return $this;
	}

	public function decreaseVerbosityLevel()
	{
		if ($this->verbosityLevel > 0)
		{
			$this->verbosityLevel--;
		}

		return $this;
	}

	public function getVerbosityLevel()
	{
		return $this->verbosityLevel;
	}

	public function resetVerbosityLevel()
	{
		$this->verbosityLevel = 0;

		return $this;
	}

	protected function setArgumentHandlers()
	{
		$this->argumentsParser->resetHandlers();

		$this->help = array();

		return $this;
	}

	protected function canRun()
	{
		return ($this->doRun === true);
	}

	protected function stopRun()
	{
		$this->doRun = false;

		return $this;
	}

	protected function writeHelpUsage()
	{
		if ($this->help)
		{
			$this->writeHelp($this->locale->_('Usage: %s [options]', $this->getName()));
		}

		return $this;
	}

	protected function writeHelpOptions()
	{
		if ($this->help)
		{
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

			$this->writeHelp($this->locale->_('Available options are:'));

			static::writeLabelsWithWriter($arguments, 1, $this->helpWriter);
		}

		return $this;
	}

	protected function parseArguments(array $arguments)
	{
		$this->argumentsParser->parse($this, $arguments);

		return $this;
	}

	protected function doRun()
	{
		return $this;
	}

	protected static function writeLabelWithWriter($label, $value, $level, writer $writer)
	{
		return $writer->write(($level <= 0 ? '' : str_repeat(self::padding, $level)) . (preg_match('/^ +$/', $label) ? $label : rtrim($label)) . ': ' . trim($value));
	}

	protected static function writeLabelsWithWriter($labels, $level, writer $writer)
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

			static::writeLabelWithWriter(str_pad($label, $maxLength, ' ', STR_PAD_LEFT), $value[0], $level, $writer);

			if (sizeof($value) > 1)
			{
				foreach (array_slice($value, 1) as $line)
				{
					static::writeLabelWithWriter(str_repeat(' ', $maxLength), $line, $level, $writer);
				}
			}
		}

		return $writer;
	}
}
