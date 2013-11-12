<?php

namespace mageekguy\atoum\writer\decorators;

use
	mageekguy\atoum\writer
;

class prompt implements writer\decorator
{
	const defaultPrompt = '$ ';

	protected $prompt = '';

	public function __construct($prompt = null)
	{
		$this->setPrompt($prompt);
	}

	public function setPrompt($prompt = null)
	{
		$this->prompt = $prompt ?: static::defaultPrompt;

		return $this;
	}

	public function getPrompt()
	{
		return $this->prompt;
	}

	public function decorate($message)
	{
		return $this->prompt . $message;
	}
}
