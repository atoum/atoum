<?php

namespace mageekguy\atoum\test\engines\fcgi;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\fcgi\requests
;

class request extends requests\post
{
	protected $localTestPath = '';

	public function __construct(atoum\test $test, atoum\fcgi\stream $stream)
	{
		$this->localTestPath = $test->getPath();

		$this->SCRIPT_FILENAME = $stream->getRemotePath(atoum\directory) . 'fcgi' . DIRECTORY_SEPARATOR . 'runner.php';

		$this['phpPath'] = $stream->getPhpPath();
		$this['atoumDirectory'] = $stream->getRemotePath(atoum\directory);
		$this['testPath'] = $stream->getRemotePath($test->getPath());
		$this['testClass'] = $test->getClass();
		$this['testMethod'] = $test->getCurrentMethod();
		$this['localeClass'] = get_class($test->getLocale());
		$this['localeValue'] = $test->getLocale()->get() ?: '';
	}

	public function getRemoteAtoumDirectory()
	{
		return $this['atoumDirectory'];
	}

	public function getRemotePhpPath()
	{
		return $this['phpPath'];
	}

	public function setRemoteTestPath($path)
	{
		$this['testPath'] = (string) $path;

		return $this;
	}

	public function getRemoteTestPath()
	{
		return $this['testPath'];
	}

	public function getLocalTestPath()
	{
		return $this->localTestPath;
	}

	public function getTestClass()
	{
		return $this['testClass'];
	}

	public function getTestMethod()
	{
		return $this['testMethod'];
	}
}
