<?php

namespace mageekguy\atoum\test\phpunit\call;


class throwing
{
	protected $exception;

	public function __construct(\exception $exception)
	{
		$this->exception = $exception;
	}

	public function getException()
	{
		return $this->exception;
	}
}