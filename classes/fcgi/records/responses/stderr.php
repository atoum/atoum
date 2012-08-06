<?php

namespace mageekguy\atoum\fcgi\records\responses;

use
	mageekguy\atoum\fcgi,
	mageekguy\atoum\fcgi\records\responses
;

class stderr extends responses\stdout
{
	const type = '7';

	public function addToResponse(fcgi\response $response)
	{
		$response->addToStderr($this);

		return $this;
	}
}
