<?php

namespace mageekguy\atoum\tests\functional\selenium;

abstract class webDriver
{
	private $webDriverHubUrl;
	
	protected $desiredCapabilities;
	
	protected function __construct($host, $port, capabilities $desiredCapabilities)
	{
		$this->setWebDriverHubUrl($host, $port);
		$this->desiredCapabilities = $desiredCapabilities;
	}
	
	protected function setWebDriverHubUrl($host, $port)
	{
		$this->webDriverHubUrl = 'http://' . $host . ':' . $port . '/wd/hub';
	}
	
	protected function getWebDriverHubUrl()
	{
		return $this->webDriverHubUrl;
	}
	
	public function close()
	{
		
	}
	
	public function findElement(by $by)
	{
		
	}
	
	public function findElements(by $by)
	{
		
	}
	
	public function get($url)
	{
		
	}
	
	public function getCurrentUrl()
	{
		
	}
	
	public function getPageSource()
	{
		
	}
	
	public function getTitle()
	{
		
	}
	
	public function getWindowHandle()
	{
		
	}
	
	public function getWindowHandles()
	{
		
	}
	
	public function manage()
	{
		
	}
	
	public function navigate()
	{
		
	}

	public function quit()
	{
		
	}

	public function switchTo()
	{
		
	}
	
	public function getDesiredCapabilities()
	{
		return $this->desiredCapabilities;
	}
}

?>
