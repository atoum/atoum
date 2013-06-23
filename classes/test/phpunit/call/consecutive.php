<?php

namespace mageekguy\atoum\test\phpunit\call;


class consecutive
{
	protected $values;

	public function __construct(array $values)
	{
		$this->values = $values;
	}

	public function getValues()
	{
		return $this->values;
	}
}