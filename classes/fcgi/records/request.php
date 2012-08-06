<?php

namespace mageekguy\atoum\fcgi\records;

use
	mageekguy\atoum\fcgi
;

abstract class request extends fcgi\record implements \countable
{
	public function __construct($type, $requestId = '1', $contentData = '')
	{
		parent::__construct($type, $requestId, $contentData);
	}

	public function count()
	{
		return strlen($this->getContentData());
	}

	public function setRequestId($requestId)
	{
		return parent::setRequestId($requestId);
	}

	public function getContentData()
	{
		return '';
	}

	public function getStreamData()
	{
		$contentData = $this->getContentData();

		$contentDataLength = strlen($contentData);

		if ($contentDataLength > self::maxContentDataLength)
		{
			throw new fcgi\record\exception('Content data length must be less than or equal to ' . self::maxContentDataLength);
		}

		list($requestIdB0, $requestIdB1) = self::toStreamValue($this->requestId);
		list($contentLengthB0, $contentLengthB1) = self::toStreamValue($contentDataLength);

		return sprintf('%c%c%c%c%c%c%c%c%s%s', self::version, $this->type, $requestIdB0, $requestIdB1, $contentLengthB0, $contentLengthB1, 0, 0, $contentData, '');
	}

	protected static function toStreamValue($value)
	{
		return array(($value >> 8) & 0xff, $value & 0xff);
	}
}
