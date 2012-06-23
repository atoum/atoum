<?php

namespace atoum\adapter;

use
	atoum
;

interface aggregator
{
	public function setAdapter(atoum\adapter $adapter);

	public function getAdapter();
}
