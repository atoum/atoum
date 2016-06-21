<?php

namespace mageekguy\atoum\report\fields\runner;

use
	mageekguy\atoum\php,
	mageekguy\atoum\adapter,
	mageekguy\atoum\exceptions\runtime,
	mageekguy\atoum\iterators,
	mageekguy\atoum\observable,
	mageekguy\atoum\runner,
	mageekguy\atoum\report
;

abstract class coverage extends report\field
{
	protected $php = null;
	protected $adapter = null;
	protected $coverage = null;
	protected $srcDirectories = array();

	public function __construct()
	{
		parent::__construct(array(runner::runStop));

		$this
			->setPhp()
			->setAdapter()
		;
	}

	public function setPhp(php $php = null)
	{
		$this->php = $php ?: new php();

		return $this;
	}

	public function getPhp()
	{
		return $this->php;
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

	public function addSrcDirectory($srcDirectory, \closure $filterClosure = null)
	{
		$srcDirectory = (string) $srcDirectory;

		if (isset($this->srcDirectories[$srcDirectory]) === false)
		{
			$this->srcDirectories[$srcDirectory] = $filterClosure === null ? array() : array($filterClosure);
		}
		else if ($filterClosure !== null)
		{
			$this->srcDirectories[$srcDirectory][] = $filterClosure;
		}

		return $this;
	}

	public function getSrcDirectories()
	{
		return $this->srcDirectories;
	}

	public function getSrcDirectoryIterators()
	{
		$iterators = array();

		foreach ($this->srcDirectories as $srcDirectory => $closures)
		{
			$iterators[] = $iterator = new \recursiveIteratorIterator(new iterators\filters\recursives\closure(new \recursiveDirectoryIterator($srcDirectory, \filesystemIterator::SKIP_DOTS|\filesystemIterator::CURRENT_AS_FILEINFO)), \recursiveIteratorIterator::LEAVES_ONLY);

			foreach ($closures as $closure)
			{
				$iterator->addClosure($closure);
			}
		}

		return $iterators;
	}

	public function getCoverage()
	{
		return $this->coverage;
	}

	public function handleEvent($event, observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else if ($observable->codeCoverageIsEnabled() === true)
		{
			$this->coverage = $observable->getScore()->getCoverage();

			if ($this->adapter->extension_loaded('xdebug') === true)
			{
				$phpCode =
					'<?php ' .
					'ob_start();' .
					'require \'' . \mageekguy\atoum\directory . '/classes/autoloader.php\';'
				;

				$autoloaderFile = $observable->getAutoloaderFile();

				if ($autoloaderFile !== null)
				{
					$phpCode .=
						'$includer = new mageekguy\atoum\includer();' .
						'try { $includer->includePath(\'' . $autoloaderFile . '\'); }' .
						'catch (mageekguy\atoum\includer\exception $exception)' .
						'{ die(\'Unable to include autoloader file \\\'' . $autoloaderFile . '\\\'\'); }'
					;
				}

				$bootstrapFile = $observable->getBootstrapFile();

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
					'$data = array(\'classes\' => get_declared_classes());' .
					'ob_start();' .
					'xdebug_start_code_coverage(XDEBUG_CC_UNUSED | XDEBUG_CC_DEAD_CODE' . ($observable->branchesAndPathsCoverageIsEnabled() === true ? ' | XDEBUG_CC_BRANCH_CHECK' : '')  . ');' .
					'require_once \'%s\';' .
					'$data[\'coverage\'] = xdebug_get_code_coverage();' .
					'xdebug_stop_code_coverage();' .
					'ob_end_clean();' .
					'$data[\'classes\'] = array_diff(get_declared_classes(), $data[\'classes\']);' .
					'echo serialize($data);'
				;

				foreach ($this->getSrcDirectoryIterators() as $srcDirectoryIterator)
				{
					foreach ($srcDirectoryIterator as $file)
					{
						if (in_array($file->getPathname(), $this->adapter->get_included_files()) === false)
						{
							if ($this->php->reset()->run(sprintf($phpCode, $file->getPathname()))->getExitCode() > 0)
							{
								throw new runtime('Unable to get default code coverage for file \'' . $file->getPathname() . '\': ' . $this->php->getStderr());
							}

							$data = unserialize($this->php->getStdOut());

							foreach ($data['classes'] as $class)
							{
								$this->coverage->addXdebugDataForClass($class, $data['coverage']);
							}
						}
					}
				}
			}

			return true;
		}
	}
}
