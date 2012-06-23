<?php

namespace mageekguy\atoum\adapter;

interface definition
{
	public function __call($functionName, $arguments);

	public function invoke($functionName, array $arguments = array());
}
