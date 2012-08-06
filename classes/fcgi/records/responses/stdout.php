<?php

namespace mageekguy\atoum\fcgi\records\responses;

use
	mageekguy\atoum\fcgi,
	mageekguy\atoum\fcgi\records
;

class stdout extends records\response
{
	const type = '6';

	protected $contentData = '';

	public function __construct($requestId, $contentData)
	{
		parent::__construct(static::type, $requestId);

		$this->contentData = (string) $contentData;
	}

	public function getContentData()
	{
		return $this->contentData;
	}

	public function addToResponse(fcgi\response $response)
	{
		$response->addToStdout($this);

		return $this;
	}
}
