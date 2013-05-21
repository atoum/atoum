<?php

namespace mageekguy\atoum\runner;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class score extends atoum\score
{
	protected $phpPath = null;
	protected $phpVersion = null;
	protected $atoumPath = null;
	protected $atoumVersion = null;

	public function reset()
	{
		$this->phpPath = null;
		$this->phpVersion = null;
		$this->atoumPath = null;
		$this->atoumVersion = null;

		return parent::reset();
	}

	public function setAtoumPath($path)
	{
		if ($this->atoumPath !== null)
		{
			throw new exceptions\runtime('Path of atoum is already set');
		}

		$this->atoumPath = (string) $path;

		return $this;
	}

	public function getAtoumPath()
	{
		return $this->atoumPath;
	}

	public function setAtoumVersion($version)
	{
		if ($this->atoumVersion !== null)
		{
			throw new exceptions\runtime('Version of atoum is already set');
		}

		$this->atoumVersion = (string) $version;

		return $this;
	}

	public function getAtoumVersion()
	{
		return $this->atoumVersion;
	}

	public function setPhpPath($path)
	{
		if ($this->phpPath !== null)
		{
			throw new exceptions\runtime('PHP path is already set');
		}

		$this->phpPath = (string) $path;

		return $this;
	}

	public function getPhpPath()
	{
		return $this->phpPath;
	}

	public function setPhpVersion($version)
	{
		if ($this->phpVersion !== null)
		{
			throw new exceptions\runtime('PHP version is already set');
		}

		$this->phpVersion = trim($version);

		return $this;
	}

	public function getPhpVersion()
	{
		return $this->phpVersion;
	}
}
