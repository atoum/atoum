<?php

namespace mageekguy\atoum\test\engines\fcgi;

use
	mageekguy\atoum
;

class stream extends atoum\fcgi\stream
{
	protected $phpPath = 'php';
	protected $mapDirectories = array();

	public function setPhpPath($path)
	{
		$this->phpPath = (string) $path;

		return $this;
	}

	public function getPhpPath()
	{
		return $this->phpPath;
	}

	public function mapRemoteDirectory($localDirectory, $remoteDirectory)
	{
		$this->mapDirectories[rtrim((string) $localDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR] = rtrim((string) $remoteDirectory, DIRECTORY_SEPARATOR);

		return $this;
	}

	public function getRemotePath($path)
	{
		foreach ($this->mapDirectories as $localDirectory => $remoteDirectory)
		{
			if (strpos($path, $localDirectory) === 0)
			{
				$path = $remoteDirectory . DIRECTORY_SEPARATOR . substr($path, strlen($localDirectory));
			}
		}

		return $path;
	}
}
