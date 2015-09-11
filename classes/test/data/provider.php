<?php

namespace mageekguy\atoum\test\data;


interface provider
{
	public function __invoke();

	public function __toString();

	public function generate();
}
