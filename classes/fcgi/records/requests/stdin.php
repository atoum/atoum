<?php

namespace mageekguy\atoum\fcgi\records\requests;

use
	mageekguy\atoum\fcgi\records
;

class stdin extends records\request
{
	const type = '5';

	protected $contentData = '';

	public function __construct($contentData = '', $requestId = 1)
	{
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
