<?php

namespace mageekguy\atoum;

use
	mageekguy\atoum,
	mageekguy\atoum\php
;

class php
{
	protected $adapter = null;
	protected $binaryPath = '';
	protected $options = array();
	protected $arguments = array();
	protected $env = array();

	private $phpProcessus = null;
	private $phpStreams = array();
	private $stdOut = '';
	private $stdErr = '';
	private $exitCode = null;

	public function __construct($phpPath = null, adapter $adapter = null)
	{
		$this
			->setAdapter($adapter)
			->setBinaryPath($phpPath)
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
					$command .= ' ' . $value;
				}
			}
		}

		if (static::osIsWindows() === true)
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

	public function setAdapter(adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new adapter();

		return $this;
	}

	public function getBinaryPath()
	{
		return $this->binaryPath;
	}

	public function setBinaryPath($phpPath = null)
	{
		$this->binaryPath = $phpPath;

		if ($this->binaryPath === null)
		{
			if ($this->adapter->defined('PHP_BINARY') === true)
			{
				$this->binaryPath = $this->adapter->constant('PHP_BINARY');
			}

			if ($this->binaryPath === null)
			{
				$this->binaryPath = $this->adapter->getenv('PHP_PEAR_PHP_BIN');

				if ($this->binaryPath === false)
				{
					$this->binaryPath = $this->adapter->getenv('PHPBIN');

					if ($this->binaryPath === false)
					{
						$phpDirectory = $this->adapter->constant('PHP_BINDIR');

						if ($phpDirectory === null)
						{
							throw new exceptions\runtime('Unable to find PHP executable');
						}

						$this->binaryPath = $phpDirectory . '/php';
					}
				}
			}
		}

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

		if ($this->phpProcessus !== null)
		{
			$this->stdOut .= $this->adapter->stream_get_contents($this->phpStreams[1]);
			$this->stdErr .= $this->adapter->stream_get_contents($this->phpStreams[2]);

			$phpStatus = $this->adapter->proc_get_status($this->phpProcessus);

			$isRunning = $phpStatus['running'];

			if ($isRunning === false)
			{
				$this->stdOut .= $this->adapter->stream_get_contents($this->phpStreams[1]);
				$this->adapter->fclose($this->phpStreams[1]);

				$this->stdErr .= $this->adapter->stream_get_contents($this->phpStreams[2]);
				$this->adapter->fclose($this->phpStreams[2]);

				$this->phpStreams = array();

				$this->exitCode = $phpStatus['exitcode'];

				$this->adapter->proc_close($this->phpProcessus);
				$this->phpProcessus = null;
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

	public function run($code = '')
	{
		if ($this->phpProcessus !== null)
		{
			throw new php\exception('Unable to run \'' . $code . '\' because php is running');
		}

		$pipes = array(
			1 => array('pipe', 'w'),
			2 => array('pipe', 'w')
		);

		if ($code != '')
		{
			$pipes[0] = array('pipe', 'r');
		}

		$this->phpProcessus = @call_user_func_array(array($this->adapter, 'proc_open'), array((string) $this, $pipes, & $this->phpStreams, null, sizeof($this->env) <= 0 ? null : $this->env));

		if ($this->phpProcessus === false)
		{
			throw new php\exception('Unable to run \'' . $code . '\' with php binary \'' . $this->binaryPath . '\'');
		}

		if (isset($this->phpStreams[0]) === true)
		{
			while ($code != '')
			{
				$codeWrited = $this->adapter->fwrite($this->phpStreams[0], $code, strlen($code));

				if ($codeWrited === false)
				{
					throw new php\exception('Unable to send \'' . $code . '\' to php binary \'' . $this->binaryPath . '\'');
				}

				$code = substr($code, $codeWrited);
			}

			$this->adapter->fclose($this->phpStreams[0]);
			unset($this->phpStreams[0]);
		}

		$this->adapter->stream_set_blocking($this->phpStreams[1], 0);
		$this->adapter->stream_set_blocking($this->phpStreams[2], 0);

		return $this;
	}

	private static function osIsWindows()
	{
		return (defined('PHP_WINDOWS_VERSION_MAJOR') === true);
	}
}
