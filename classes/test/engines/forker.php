<?php

namespace mageekguy\atoum\test\engines;

use
	mageekguy\atoum
;

class forker
{
	protected $testClass = '';
	protected $testMethod = '';
	protected $testPath = '';
	protected $stdOut = '';
	protected $stdErr = '';
	protected $factory = null;

	private $php = null;
	private $pipes = array();

	public function __construct(atoum\factory $factory)
	{
		$this->setFactory($factory);
	}

	public function setFactory(atoum\factory $factory)
	{
		$this->factory = $factory;

		return $this;
	}

	public function getFactory()
	{
		return $this->factory;
	}

	public function run(atoum\test $test, $method)
	{
		$this->testClass = $test->getClass();
		$this->testMethod = (string) $method;
		$this->testPath = $test->getPath();

		$phpPath = $test->getPhpPath();

		$phpCode =
			'<?php ' .
			'define(\'mageekguy\atoum\autorun\', false);' .
			'require \'' . atoum\directory . '/scripts/runner.php\';'
		;

		$bootstrapFile = $test->getBootstrapFile();

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
			'require \'' . $test->getPath() . '\';' .
			'$test = new ' . $this->testClass . '();' .
			'$test->setLocale(new ' . get_class($test->getLocale()) . '(' . $test->getLocale()->get() . '));' .
			'$test->setPhpPath(\'' . $phpPath . '\');'
		;

		if ($test->codeCoverageIsEnabled() === false)
		{
			$phpCode .= '$test->disableCodeCoverage();';
		}
		else
		{
			$phpCode .= '$coverage = $test->getCoverage();';

			foreach ($test->getCoverage()->getExcludedClasses() as $excludedClass)
			{
				$phpCode .= '$coverage->excludeClass(\'' . $excludedClass . '\');';
			}

			foreach ($test->getCoverage()->getExcludedNamespaces() as $excludedNamespace)
			{
				$phpCode .= '$coverage->excludeNamespace(\'' . $excludedNamespace . '\');';
			}

			foreach ($test->getCoverage()->getExcludedDirectories() as $excludedDirectory)
			{
				$phpCode .= '$coverage->excludeDirectory(\'' . $excludedDirectory . '\');';
			}
		}

		$phpCode .= 'echo serialize($test->registerMockAutoloader()->runTestMethod(\'' . $this->testMethod . '\')->getScore());';

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

				$score = $this->factory->build('mageekguy\atoum\score');

				$testScore = @unserialize($this->stdOut);

				if ($testScore instanceof atoum\score)
				{
					$score = $testScore;
				}
				else
				{
					$score->addUncompletedMethod($this->testClass, $this->testMethod, $phpStatus['exitcode'], $this->stdOut);
				}

				if ($this->stdErr !== '')
				{
					if (preg_match_all('/([^:]+): (.+) in (.+) on line ([0-9]+)/', trim($this->stdErr), $errors, PREG_SET_ORDER) === 0)
					{
						$score->addError($this->testPath, null, $this->testClass, $this->testMethod, 'UNKNOWN', $this->stdErr);
					}
					else foreach ($errors as $error)
					{
						$score->addError($this->testPath, null, $this->testClass, $this->testMethod, $error[1], $error[2], $error[3], $error[4]);
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
