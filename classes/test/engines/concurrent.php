<?php

namespace mageekguy\atoum\test\engines;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class concurrent extends test\engine
{
	protected $test = null;
	protected $method = '';
	protected $factory = null;
	protected $stdOut = '';
	protected $stdErr = '';

	private $adapter = null;
	private $php = null;
	private $pipes = array();

	public function __construct(atoum\factory $factory = null)
	{
		parent::__construct($factory);

		$this->adapter = $this->factory['mageekguy\atoum\adapter']();
	}

	public function isRunning()
	{
		return ($this->php !== null);
	}

	public function isAsynchronous()
	{
		return true;
	}

	public function run(atoum\test $test)
	{
		$currentTestMethod = $test->getCurrentMethod();

		if ($currentTestMethod !== null)
		{
			$this->test = $test;
			$this->method = $currentTestMethod;
			$this->stdOut = '';
			$this->stdErr = '';

			$phpPath = $this->test->getPhpPath();

			$this->php = @$this->adapter->invoke('proc_open', array(escapeshellarg($phpPath), array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), & $this->pipes));

			if ($this->php === false)
			{
				throw new exceptions\runtime('Unable to use \'' . $phpPath . '\'');
			}

			$phpCode =
				'<?php ' .
				'ob_start();' .
				'define(\'mageekguy\atoum\autorun\', false);' .
				'require \'' . atoum\directory . '/scripts/runner.php\';'
			;

			$bootstrapFile = $this->test->getBootstrapFile();

			if ($bootstrapFile !== null)
			{
				$phpCode .=
					'require \'' . atoum\directory . '/classes/includer.php\';' .
					'$includer = new mageekguy\atoum\includer();' .
					'try { $includer->includePath(\'' . $bootstrapFile . '\'); }' .
					'catch (mageekguy\atoum\includer\exception $exception)' .
					'{ die(\'Unable to include bootstrap file \\\'' . $bootstrapFile . '\\\'\'); }'
				;
			}

			$phpCode .=
				'require \'' . $this->test->getPath() . '\';' .
				'$test = new ' . $this->test->getClass() . '();' .
				'$test->setLocale(new ' . get_class($this->test->getLocale()) . '(' . $this->test->getLocale()->get() . '));' .
				'$test->setPhpPath(\'' . $phpPath . '\');'
			;

			if ($this->test->codeCoverageIsEnabled() === false)
			{
				$phpCode .= '$test->disableCodeCoverage();';
			}
			else
			{
				$phpCode .= '$coverage = $test->getCoverage();';

				foreach ($this->test->getCoverage()->getExcludedClasses() as $excludedClass)
				{
					$phpCode .= '$coverage->excludeClass(\'' . $excludedClass . '\');';
				}

				foreach ($this->test->getCoverage()->getExcludedNamespaces() as $excludedNamespace)
				{
					$phpCode .= '$coverage->excludeNamespace(\'' . $excludedNamespace . '\');';
				}

				foreach ($this->test->getCoverage()->getExcludedDirectories() as $excludedDirectory)
				{
					$phpCode .= '$coverage->excludeDirectory(\'' . $excludedDirectory . '\');';
				}
			}

			$phpCode .=
				'ob_end_clean();' .
				'echo serialize($test->runTestMethod(\'' . $this->method . '\')->getScore());'
			;

			$this->adapter->fwrite($this->pipes[0], $phpCode);
			$this->adapter->fclose($this->pipes[0]);
			unset($this->pipes[0]);

			$this->adapter->stream_set_blocking($this->pipes[1], 0);
			$this->adapter->stream_set_blocking($this->pipes[2], 0);
		}

		return $this;
	}

	public function getScore()
	{
		$score = null;

		if ($this->php !== null)
		{
			$phpStatus = $this->adapter->proc_get_status($this->php);

			if ($phpStatus['running'] == true)
			{
				$this->stdOut .= $this->adapter->stream_get_contents($this->pipes[1]);
				$this->stdErr .= $this->adapter->stream_get_contents($this->pipes[2]);
			}
			else
			{
				$this->stdOut .= $this->adapter->stream_get_contents($this->pipes[1]);
				$this->adapter->fclose($this->pipes[1]);

				$this->stdErr .= $this->adapter->stream_get_contents($this->pipes[2]);
				$this->adapter->fclose($this->pipes[2]);

				$this->pipes = array();

				$this->adapter->proc_close($this->php);
				$this->php = null;

				$score = @unserialize($this->stdOut);

				if ($score instanceof atoum\score === false)
				{
					$score = $this->factory['mageekguy\atoum\score']($this->factory);
					$score->addUncompletedMethod($this->test->getClass(), $this->method, $phpStatus['exitcode'], $this->stdOut);
				}

				if ($this->stdErr !== '')
				{
					if (preg_match_all('/([^:]+): (.+) in (.+) on line ([0-9]+)/', trim($this->stdErr), $errors, PREG_SET_ORDER) === 0)
					{
						$score->addError($this->test->getPath(), null, $this->test->getClass(), $this->method, 'UNKNOWN', $this->stdErr);
					}
					else foreach ($errors as $error)
					{
						$score->addError($this->test->getPath(), null, $this->test->getClass(), $this->method, $error[1], $error[2], $error[3], $error[4]);
					}
				}

				$this->stdOut = '';
				$this->stdErr = '';
			}
		}

		return $score;
	}
}
