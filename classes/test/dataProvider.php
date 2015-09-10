<?php

namespace mageekguy\atoum\test;


interface dataProvider
{
	public function __invoke();

	public function __toString();

	public function generate();
}
