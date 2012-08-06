<?php

namespace mageekguy\atoum\fcgi\records;

use
	mageekguy\atoum\fcgi
;

abstract class response extends fcgi\record
{
	public function addToResponse(fcgi\response $response)
	{
		return $this;
	}
}
