<?php

namespace mageekguy\atoum\cli;

use
	mageekguy\atoum
;

class command
{
	protected $adapter = null;
	protected $binaryPath = '';
	protected $options = array();
	protected $arguments = array();
	protected $env = array();

	private $processus = null;
	private $streams = array();
	private $stdOut = '';
	private $stdErr = '';
	private $exitCode = null;

	public function __construct($binaryPath = null, atoum\adapter $adapter = null)
	{
		$this
			->setAdapter($adapter)
			->setBinaryPath($binaryPath)
		;
	}

	public function __toString()
	{
		$command = '';

		foreach ($this->options as $option => $value)
		{
			$command .= ' ' . $option;

			if ($value !== null)
			{
				$command .= ' ' . $value;
			}
		}

		if (sizeof($this->arguments) > 0)
		{
			$command .= ' --';

			foreach ($this->arguments as $argument)
			{
				$command .= ' ' . key($argument);

				$value = current($argument);

				if ($value !== null)
				{
					$command .= ' ' . escapeshellarg($value);
				}
			}
		}

		if (self::osIsWindows() === true)
		{
			$command = '"' . $this->binaryPath . '"' . $command;
		}
		else
		{
			$command = escapeshellcmd($this->binaryPath . $command);
		}

		return $command;
	}

	public function __set($envVariable, $value)
	{
		$this->env[$envVariable] = $value;

		return $this;
	}

	public function __get($envVariable)
	{
		return (isset($this->{$envVariable}) === false ? null : $this->env[$envVariable]);
	}

	public function __isset($envVariable)
	{
		return (isset($this->env[$envVariable]) === true);
	}

	public function __unset($envVariable)
	{
		if (isset($this->{$envVariable}) === true)
		{
			unset($this->env[$envVariable]);
		}

		return $this;
	}

	public function reset()
	{
		$this->options = array();
		$this->arguments = array();
		$this->stdOut = '';
		$this->stdErr = '';
		$this->exitCode = null;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new atoum\adapter();

		return $this;
	}

	public function getBinaryPath()
	{
		return $this->binaryPath;
	}

	public function setBinaryPath($binaryPath = null)
	{
		$this->binaryPath = (string) $binaryPath;

		return $this;
	}

	public function addOption($option, $value = null)
	{
		$this->options[$option] = $value ?: null;

		return $this;
	}

	public function getOptions()
	{
		return $this->options;
	}

	public function addArgument($argument, $value = null)
	{
		$this->arguments[] = array($argument => $value ?: null);

		return $this;
	}

	public function getArguments()
	{
		return $this->arguments;
	}

	public function isRunning()
	{
		$isRunning = false;

		if ($this->processus !== null)
		{
			$this->stdOut .= $this->adapter->stream_get_contents($this->streams[1]);
			$this->stdErr .= $this->adapter->stream_get_contents($this->streams[2]);

			$processusStatus = $this->adapter->proc_get_status($this->processus);

			$isRunning = $processusStatus['running'];

			if ($isRunning === false)
			{
				$this->stdOut .= $this->adapter->stream_get_contents($this->streams[1]);
				$this->adapter->fclose($this->streams[1]);

				$this->stdErr .= $this->adapter->stream_get_contents($this->streams[2]);
				$this->adapter->fclose($this->streams[2]);

				$this->streams = array();

				$this->exitCode = $processusStatus['exitcode'];

				$this->adapter->proc_close($this->processus);
				$this->processus = null;
			}
		}

		return $isRunning;
	}

	public function getStdout()
	{
		return $this->stdOut;
	}

	public function getStderr()
	{
		return $this->stdErr;
	}

	public function getExitCode()
	{
		while ($this->isRunning() === true);

		return $this->exitCode;
	}

	public function run($stdin = '')
	{
		if ($this->processus !== null)
		{
			throw new command\exception('Unable to run \'' . $this . '\' because is currently running');
		}

		$pipes = array(
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		if ($stdin != '')
		{
			$pipes[0] = array('pipe', 'r');
		}

		$this->processus = @call_user_func_array(array($this->adapter, 'proc_open'), array((string) $this, $pipes, & $this->streams, null, sizeof($this->env) <= 0 ? null : $this->env));

		if ($this->processus === false)
		{
			throw new command\exception('Unable to run \'' . $this . '\'');
		}

		if (isset($this->streams[0]) === true)
		{
			while ($stdin != '')
			{
				$stdinWrited = $this->adapter->fwrite($this->streams[0], $stdin, strlen($stdin));

				if ($stdinWrited === false)
				{
					throw new command\exception('Unable to send \'' . $stdin . '\' to \'' . $this . '\'');
				}

				$stdin = substr($stdin, $stdinWrited);
			}

			$this->adapter->fclose($this->streams[0]);
			unset($this->streams[0]);
		}

		$this->adapter->stream_set_blocking($this->streams[1], 0);
		$this->adapter->stream_set_blocking($this->streams[2], 0);

		return $this;
	}

	private static function osIsWindows()
	{
		return (defined('PHP_WINDOWS_VERSION_MAJOR') === true);
	}
}
