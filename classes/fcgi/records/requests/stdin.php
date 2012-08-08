<?php

namespace mageekguy\atoum\fcgi\records\requests;

use
	mageekguy\atoum\fcgi\record,
	mageekguy\atoum\fcgi\records
;

class stdin extends records\request
{
	const type = '5';

	protected $contentData = '';

	public function __construct($contentData = '', $requestId = 1)
	{
		if ($requestId < 1)
		{
			throw new record\exception('Request ID must be greater than 0');
		}

		parent::__construct(self::type, $requestId);

		$this->setContentData($contentData);
	}

	public function setContentData($contentData)
	{
		$this->contentData = (string) $contentData;

		return $this;
	}

	public function getContentData()
	{
		return $this->contentData;
	}
}
