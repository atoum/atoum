<?php

namespace mageekguy\atoum\tests\functional\selenium;

class capabilities
{
	protected $browserName;

	protected $platform;

	protected $version;

	protected $javascriptEnabled;

	protected $takingScreenshot;

	protected $handlingAlerts;

	protected $nativeEvents;

	public function __toString()
	{
		$string = '{desiredCapabilities:{';
		$startingLength = strlen($string);

		if (isset($this->browserName))
		{
			$string .= 'browserName: \'' . $this->browserName . '\'';
		}
		if (isset($this->platform))
		{
			$string .= (strlen($string) > $startingLength) ? ', ' : '';
			$string .= 'platform: \'' . $this->platform . '\'';
		}
		if (isset($this->version))
		{
			$string .= (strlen($string) > $startingLength) ? ', ' : '';
			$string .= 'version: \'' . $this->version . '\'';
		}
		if (isset($this->javascriptEnabled))
		{
			$string .= (strlen($string) > $startingLength) ? ', ' : '';
			$string .= 'javascriptEnabled: ' . ($this->javascriptEnabled ? 'true' : 'false');
		}
		if (isset($this->takingScreenshot))
		{
			$string .= (strlen($string) > $startingLength) ? ', ' : '';
			$string .= 'takesScreenshot: ' . ($this->takingScreenshot ? 'true' : 'false');
		}
		if (isset($this->handlingAlerts))
		{
			$string .= (strlen($string) > $startingLength) ? ', ' : '';
			$string .= 'handlesAlerts: ' . ($this->handlingAlerts ? 'true' : 'false');
		}
		if (isset($this->nativeEvents))
		{
			$string .= (strlen($string) > $startingLength) ? ', ' : '';
			$string .= 'nativeEvents: ' . ($this->nativeEvents ? 'true' : 'false');
		}

		$string .= '}}';

		return $string;
	}

	public function getBrowserName()
	{
		return $this->browserName;
	}

	public function setBrowserName($browserName)
	{
		$this->browserName = $browserName;
	}

	public function getPlatform()
	{
		return $this->platform;
	}

	public function setPlatform($platform)
	{
		$this->platform = $platform;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function setVersion($version)
	{
		$this->version = $version;
	}

	public function isJavascriptEnabled()
	{
		return $this->javascriptEnabled;
	}

	public function setJavascriptEnabled($javascriptEnabled)
	{
		$this->javascriptEnabled = (bool)$javascriptEnabled;
	}

	public function isTakingScreenshot()
	{
		return $this->takingScreenshot;
	}

	public function setTakingScreenshot($isTakingScreenshot)
	{
		$this->takingScreenshot = (bool)$isTakingScreenshot;
	}

	public function isHandlingAlerts()
	{
		return $this->handlingAlerts;
	}

	public function setHandlingAlerts($isHandlingAlerts)
	{
		$this->handlingAlerts = (bool)$isHandlingAlerts;
	}

	public function hasNativeEvents()
	{
		return $this->nativeEvents;
	}

	public function setNativeEvents($hasNativeEvents)
	{
		$this->nativeEvents = (bool)$hasNativeEvents;
	}
}

?>
