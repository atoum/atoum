<?php

namespace mageekguy\atoum\test\engines;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\exceptions
;

class concurrent extends test\engine
{
	protected $scoreFactory = null;
	protected $php = null;
	protected $test = null;
	protected $method = '';

	public function __construct()
	{
		$this
			->setScoreFactory()
			->setPhp()
		;
	}

	public function setScoreFactory(\closure $factory = null)
	{
		$this->scoreFactory = $factory ?: function() { return new atoum\score(); };

		return $this;
	}

	public function getScoreFactory()
	{
		return $this->scoreFactory;
	}

	public function setPhp(atoum\php $php = null)
	{
		$this->php = $php ?: new atoum\php();

		return $this;
	}

	public function getPhp()
	{
		return $this->php;
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

			$phpPath = $this->test->getPhpPath();

			$phpCode =
				'<?php ' .
				'ob_start();' .
				'require \'' . atoum\directory . '/classes/autoloader.php\';'
			;

			$autoloaderFile = $this->test->getAutoloaderFile();

			if ($autoloaderFile !== null)
			{
				$phpCode .=
					'$includer = new mageekguy\atoum\includer();' .
					'try { $includer->includePath(\'' . $autoloaderFile . '\'); }' .
					'catch (mageekguy\atoum\includer\exception $exception)' .
					'{ die(\'Unable to include autoloader file \\\'' . $autoloaderFile . '\\\'\'); }'
				;
			}

			$bootstrapFile = $this->test->getBootstrapFile();

			if ($bootstrapFile !== null)
			{
				$phpCode .=
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

			if ($this->test->debugModeIsEnabled() === true)
			{
				$phpCode .= '$test->enableDebugMode();';
			}

			if ($this->test->codeCoverageIsEnabled() === false)
			{
				$phpCode .= '$test->disableCodeCoverage();';
			}
			else
			{
				if ($this->test->branchesAndPathsCoverageIsEnabled() === true)
				{
					$phpCode .= '$test->enableBranchesAndPathsCoverage();';
				}

				$phpCode .= '$coverage = $test->getCoverage();';

				foreach ($this->test->getCoverage()->getExcludedMethods() as $excludedMethod)
				{
					$phpCode .= '$coverage->excludeMethod(\'' . $excludedMethod . '\');';
				}

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

			if ($this->test->getMockGenerator()->undefinedMethodUsageIsAllowed() === false)
			{
				$phpCode .= '$test->getMockGenerator()->disallowUndefinedMethodUsage();';
			}

			$extensions = $test->getExtensions();

			foreach ($extensions as $extension)
			{
				$configuration = $exception = null;
				$extensionClass = get_class($extension);

				try
				{
					$configuration = $extensions[$extension];
				}
				catch (\unexpectedValueException $exception) {}

				if ($exception !== null || $configuration === null)
				{
					$phpCode .= '$test->addExtension(new ' . $extensionClass . ');';
				}
				else
				{
					$configurationClass = get_class($configuration);
					$serialized = $configuration->serialize();

					if (is_array($serialized) === false)
					{
						throw new exceptions\logic('Extension (' . $extensionClass . ') configuration (' . $configurationClass . ') should serialize as an array');
					}

					$phpCode .= '$test->addExtension(new ' . $extensionClass . ', ' . $configurationClass . '::unserialize(' . var_export($serialized, true) . '));';
				}
			}

			$phpCode .=
				'ob_end_clean();' .
				'mageekguy\atoum\scripts\runner::disableAutorun();' .
				'echo serialize($test->runTestMethod(\'' . $this->method . '\')->getScore());'
			;

			$xdebugConfig = $test->getXdebugConfig();

			if ($xdebugConfig !== null)
			{
				if (getenv('XDEBUG_CONFIG') !== false || ini_get('xdebug.remote_autostart') != 0)
				{
					throw new exceptions\runtime('XDEBUG_CONFIG variable must not be set or value of xdebug.remote_autostart must be 0 to use xdebug with concurrent engine');
				}

				$this->php->XDEBUG_CONFIG = $xdebugConfig;
				if (getenv('PHP_IDE_CONFIG') !== false) {
					$this->php->PHP_IDE_CONFIG = getenv('PHP_IDE_CONFIG');
				}
			}

			$this->php
				->reset()
				->setBinaryPath($phpPath)
				->run($phpCode)
			;
		}

		return $this;
	}

	public function getScore()
	{
		$score = null;

		if ($this->test !== null && $this->php->isRunning() === false)
		{
			$stdOut = $this->php->getStdout();

			$score = @unserialize($stdOut);

			if ($score instanceof atoum\score === false)
			{
				$score = call_user_func($this->scoreFactory)->addUncompletedMethod($this->test->getPath(), $this->test->getClass(), $this->method, $this->php->getExitCode(), $this->php->getStdOut());
			}

			$stdErr = $this->php->getStderr();

			if ($stdErr !== '')
			{
				if (preg_match_all('/([^:]+): (.+) in (.+) on line ([0-9]+)/', trim($stdErr), $errors, PREG_SET_ORDER) === 0)
				{
					$score->addError($this->test->getPath(), $this->test->getClass(), $this->method, null, 'UNKNOWN', $stdErr);
				}
				else foreach ($errors as $error)
				{
					$score->addError($this->test->getPath(), $this->test->getClass(), $this->method, null, $error[1], $error[2], $error[3], $error[4]);
				}
			}
		}

		return $score;
	}
}
