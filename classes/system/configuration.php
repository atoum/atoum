<?php

namespace mageekguy\atoum\system;

use
	mageekguy\atoum\exceptions
;

class configuration implements \serializable
{
	protected $data = array();

	public function __construct()
	{
		$this->data = array(
			'OS' => array(
					'version' => php_uname('s'),
					'arch' => php_uname('m')
				),
			'PHP' => array(
					'version' => phpversion(),
					'extensions' => array_merge(get_loaded_extensions(false), get_loaded_extensions(true))
				)
		);
	}

	public function __toString()
	{
		return '=> OS:' . PHP_EOL .
			'==> Version: ' . $this->getOsVersion() . PHP_EOL .
			'==> Architecture: ' .$this->getOsArchitecture() . PHP_EOL .
			'=> PHP:' . PHP_EOL .
			'==> Version: ' . $this->getPhpVersion() . PHP_EOL .
			'==> Extensions:' . PHP_EOL . '===> ' . join(PHP_EOL . '===> ', $this->getPhpExtensions()) . PHP_EOL
		;
	}

	public function getOsVersion()
	{
		return $this->data['OS']['version'];
	}

	public function getOsArchitecture()
	{
		return $this->data['OS']['arch'];
	}

	public function getPhpVersion()
	{
		return $this->data['PHP']['version'];
	}

	public function getPhpExtensions()
	{
		return $this->data['PHP']['extensions'];
	}


	public function serialize()
	{
		return serialize($this->get());
	}

	public function unserialize($string)
	{
		$data = @unserialize($string);

		if ($data === false)
		{
			throw new exception('Unable to unserialize \'' . $string . '\'');
		}

		$isConfigurationData = true;

		switch (true)
		{
			case isset($data['OS']) === false:
			case is_array($data['OS']) === false:
			case isset($data['OS']['version']) === false:
			case is_string($data['OS']['version']) === false:
			case isset($data['OS']['arch']) === false:
			case is_string($data['OS']['arch']) === false:
			case isset($data['PHP']) === false:
			case is_array($data['PHP']) === false:
			case isset($data['PHP']['version']) === false:
			case is_string($data['PHP']['version']) === false:
			case isset($data['PHP']['extensions']) === false:
			case is_array($data['PHP']['extensions']) === false:
				$isConfigurationData = false;
				break;

			default:
				foreach ($data['PHP']['extensions'] as $extension)
				{
					if (is_string($extension) === false)
					{
						$isConfigurationData = false;
					}
				}
		}

		if ($isConfigurationData === true)
		{
			$this->data = $data;
		}
		else
		{
			throw new configuration\exception('Unable to get data from \'' . $string . '\'');
		}
	}

	public function getSignature()
	{
		return sha1(serialize($this));
	}

	public function isEqualTo(configuration $configuration)
	{
		return ($this->getSignature() == $configuration->getSignature());
	}

	public function get()
	{
		return $this->data;
	}
}

?>
