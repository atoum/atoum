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
	protected $contentData = '';

	public function __construct($type, $requestId, $contentData)
	{
		if (bindec(sprintf('%08b', $type)) > self::maxType)
		{
			throw new fcgi\record\exception('Type must be less than or equal to ' . self::maxType);
		}

		if (bindec(sprintf('%016b', $requestId)) > self::maxRequestId)
		{
			throw new fcgi\record\exception('Request ID must be less than or equal to ' . self::maxRequestId);
		}

		if (strlen($contentData) > self::maxContentDataLength)
		{
			throw new fcgi\record\exception('Content data length must be less than or equal to ' . self::maxContentDataLength);
		}

		$this->type = (string) $type;
		$this->requestId = (string) $requestId;
		$this->contentData = (string) $contentData;
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
		return $this->contentData;
	}
}
