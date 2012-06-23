<?php

namespace mageekguy\atoum\adapter;

use
	mageekguy\atoum
;

interface aggregator
{
	public function setAdapter(atoum\adapter $adapter);

	public function getAdapter();
}
