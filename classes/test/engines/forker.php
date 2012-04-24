<?php

namespace mageekguy\atoum\test\engines;

use
	mageekguy\atoum,
	mageekguy\atoum\test
;

class forker extends test\engine
{
	protected $test = null;
	protected $method = '';
	protected $factory = null;
	protected $stdOut = '';
	protected $stdErr = '';

	private $php = null;
	private $pipes = array();

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

			$phpCode =
				'<?php ' .
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

			$phpCode .= 'echo serialize($test->registerMockAutoloader()->runTestMethod(\'' . $this->method . '\')->getScore());';

			$this->php = @proc_open(
				escapeshellarg($phpPath),
				array(
					0 => array('pipe', 'r'),
					1 => array('pipe', 'w'),
					2 => array('pipe', 'w')
				),
				$this->pipes
			);

			fwrite($this->pipes[0], $phpCode);
			fclose($this->pipes[0]);
			unset($this->pipes[0]);

			stream_set_blocking($this->pipes[1], 0);
			stream_set_blocking($this->pipes[2], 0);
		}

		return $this;
	}

	public function getScore()
	{
		$score = null;

		if ($this->php !== null)
		{
			$phpStatus = proc_get_status($this->php);

			if ($phpStatus['running'] == false)
			{
				$this->stdOut = stream_get_contents($this->pipes[1]);
				fclose($this->pipes[1]);

				$this->stdErr = stream_get_contents($this->pipes[2]);
				fclose($this->pipes[2]);

				$this->pipes = array();

				proc_close($this->php);
				$this->php = null;

				$score = @unserialize($this->stdOut);

				if ($score instanceof atoum\score === false)
				{
					$score = $this->factory['mageekguy\atoum\score']();

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

?>
