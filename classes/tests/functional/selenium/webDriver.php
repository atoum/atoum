<?php

namespace mageekguy\atoum\tests\functional\selenium;

use
	mageekguy\atoum\exceptions\logic,
	mageekguy\atoum\tests\functional\selenium
;

abstract class webDriver
{
	private $requestUrl;
	
	protected $_curl;
	
	protected $desiredCapabilities;
	
	public function __construct($host = 'localhost', $port = '4444', capabilities $desiredCapabilities = null)
	{
		if ($desiredCapabilities == null)
		{
			$desiredCapabilities = new selenium\capabilities();
			$desiredCapabilities->setBrowserName($this->getBrowserName());
		}
		else if ($desiredCapabilities->getBrowserName() != $this->getBrowserName())
		{
			throw new logic\invalidArgument('Desired browser name does not math this webdriver implementation');
		}
		
		$this->requestUrl = 'http://' . $host . ':' . $port . '/wd/hub';
		$this->desiredCapabilities = $desiredCapabilities;
		
		$request = $this->requestUrl . "/session";
		$session = $this->curlInit($request);
		$this->preparePost($session, (string)($this->desiredCapabilities));
		curl_setopt($session, CURLOPT_HEADER, true);
		$response = curl_exec($session);
		$header = curl_getinfo($session);
		$this->requestUrl = $header['url'];
	}
	
	protected abstract function getBrowserName();
	
	public function close()
	{
		$request = $this->requestUrl;
		$session = $this->curlInit($request);
		$this->prepareDelete($session);
		$response = curl_exec($session);
		$this->curlClose();
	}

	public function get($url)
	{
		$request = $this->requestUrl . "/url";
		$session = $this->curlInit($request);
		$args = array('url' => $url);
		$this->preparePost($session, json_encode($args));
		$response = curl_exec($session);
	}
	
	public function findElement(by $by)
	{
		
	}
	
	public function findElements(by $by)
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
		$response = $this->executeRestRequestGet($this->requestUrl . "/title");
		return $this->extractValueFromJsonResponse($response);
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
	
	protected function &curlInit($url)
	{
		if ($this->_curl === null)
		{
			$this->_curl = curl_init($url);
		}
		else
		{
			curl_setopt($this->_curl, CURLOPT_HTTPGET, true);
			curl_setopt($this->_curl, CURLOPT_URL, $url);
		}
		curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array("application/json;charset=UTF-8"));
		curl_setopt($this->_curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->_curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->_curl, CURLOPT_HEADER, false);
		return $this->_curl;
	}

	protected function curlClose()
	{
		if ($this->_curl !== null)
		{
			curl_close($this->_curl);
			$this->_curl = null;
		}
	}

	protected function preparePost($session, $postargs)
	{
		curl_setopt($session, CURLOPT_POST, true);
		if ($postargs)
		{
			curl_setopt($session, CURLOPT_POSTFIELDS, $postargs);
		}
	}

	protected function executeRestRequestPost($request, $postargs)
	{
		$session = $this->curlInit($request);
		$this->preparePost($session, $postargs);
		$response = trim(curl_exec($session));
		return $response;
	}

	protected function prepareGet($session)
	{
		//curl_setopt($session, CURLOPT_GET, true);
	}

	protected function prepareDelete($session)
	{
		curl_setopt($session, CURLOPT_CUSTOMREQUEST, 'DELETE');
	}

	public function executeRestRequestGet($request)
	{
		$session = $this->curlInit($request);
		$this->prepareGET($session);
		$response = curl_exec($session);
		return $response;
	}

	protected function handleError($session, $response)
	{
		$last_error = curl_errno($session);
		if ($last_error == 500) // selenium error
		{
			throw new WebDriverException($message, $code, $previous);
		}
		else
		{
			if ($last_error != 0) // unknown error
			{
				throw new WebDriverException($message, $code, $previous);
			}
		}
	}

	protected function handleResponse($json_response)
	{
		$status = $json_response->{'status'};

		switch ($status)
		{
			case selenium\responseStatus::Success:
				return;
			break;
			
			case selenium\responseStatus::NoSuchElement:
				throw new Exception('No such element:' . $json_response);
			break;

			default:
				throw new Exception('WebDriver exception: ' . $status);
			break;
		}
	}

	public function extractValueFromJsonResponse($json)
	{
		$json = json_decode(trim($json));
		if ($json && isset($json->value))
		{
			return $json->value;
		}
		return null;
	}
}

?>
