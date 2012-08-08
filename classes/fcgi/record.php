<?php

namespace mageekguy\atoum\fcgi;

use
	mageekguy\atoum\fcgi
;

abstract class record
{
	const version = '1';
	const maxType = 255;
	const maxRequestId = 65535;
	const maxContentDataLength = 65535;

	protected $type = '';
	protected $requestId = '';

	public function __construct($type, $requestId)
	{
		if (bindec(sprintf('%08b', $type)) > self::maxType)
		{
			throw new fcgi\record\exception('Type must be less than or equal to ' . self::maxType);
		}

		$this
			->setRequestId($requestId)
			->type = (string) $type
		;
	}

	public function getType()
	{
		return $this->type;
	}

	public function getRequestId()
	{
		return $this->requestId;
	}

	public function getContentData()
	{
		return '';
	}

	protected function setRequestId($requestId)
	{
		if (bindec(sprintf('%016b', $requestId)) > self::maxRequestId)
		{
			throw new fcgi\record\exception('Request ID must be less than or equal to ' . self::maxRequestId);
		}

		$this->requestId = (string) $requestId;

		return $this;
	}
}
