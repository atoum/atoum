<?php

namespace mageekguy\atoum\fcgi\records\requests;

use
	mageekguy\atoum\fcgi\record,
	mageekguy\atoum\fcgi\records
;

class params extends records\request
{
	const type = '4';

	protected $values = array();

	public function __construct(array $values = array(), $requestId = 1)
	{
		parent::__construct(self::type, $requestId);

		foreach ($values as $name => $value)
		{
			$this->{$name} = $value;
		}
	}

	public function __set($name, $value)
	{
		$this->values[self::getValueName($name)] = trim((string) $value);

		return $this;
	}

	public function __get($name)
	{
		$name = self::getValueName($name);

		return (isset($this->values[$name]) === false ? null : $this->values[$name]);
	}

	public function __isset($name)
	{
		return isset($this->values[self::getValueName($name)]);
	}

	public function __unset($name)
	{
		$name = self::getValueName($name);

		if (isset($this->values[$name]) === true)
		{
			unset($this->values[$name]);
		}

		return $this;
	}

	public function getValues()
	{
		return $this->values;
	}

	public function getContentData()
	{
		$contentData = '';

		foreach($this->values as $name => $value)
		{
			$contentData .= self::toStreamLength($name) . self::toStreamLength($value) . $name . $value;
		}

		return $contentData;
	}

	private static function toStreamLength($string)
	{
		$length = strlen($string);

		return ($length < 128 ? sprintf('%c', $length) : sprintf('%c%c%c%c', ($length >> 24) | 0x80, ($length >> 16) & 0xff, ($length >> 8) & 0xff, $length & 0xff));
	}

	private static function getValueName($name)
	{
		$valueName = strtoupper(trim($name));

		switch ($valueName)
		{
			case 'AUTH_TYPE':
			case 'CONTENT_LENGTH':
			case 'CONTENT_TYPE':
			case 'GATEWAY_INTERFACE':
			case 'PATH_INFO':
			case 'PATH_TRANSLATED':
			case 'QUERY_STRING':
			case 'REMOTE_ADDR':
			case 'REMOTE_HOST':
			case 'REMOTE_IDENT':
			case 'REMOTE_USER':
			case 'REQUEST_METHOD':
			case 'SCRIPT_NAME':
			case 'SCRIPT_FILENAME':
			case 'SERVER_NAME':
			case 'SERVER_PORT':
			case 'SERVER_PROTOCOL':
			case 'SERVER_SOFTWARE':
				return $valueName;

			default:
				throw new record\exception('Value \'' . $name . '\' is unknown');
		}
	}
}
