<?php

namespace mageekguy\atoum\scripts\runner\loopers;

use
	mageekguy\atoum,
	mageekguy\atoum\script,
	mageekguy\atoum\writers,
	mageekguy\atoum\scripts\runner\looper
;

class prompt implements looper
{
	private $writer;
	private $prompt;
	private $locale;
	private $cli;

	public function __construct(script\prompt $prompt = null, atoum\writer $writer = null, atoum\cli $cli = null, atoum\locale $locale = null)
	{
		$this
			->setCli($cli)
			->setOutputWriter($writer)
			->setPrompt($prompt)
			->setLocale($locale)
		;
	}

	public function setCli(atoum\cli $cli = null)
	{
		$this->cli = $cli ?: new atoum\cli();

		return $this;
	}

	public function getCli()
	{
		return $this->cli;
	}

	public function setOutputWriter(atoum\writer $writer = null)
	{
		$this->writer = $writer ?: new writers\std\out($this->cli);

		return $this;
	}

	public function getOutputWriter()
	{
		return $this->writer;
	}

	public function setPrompt(script\prompt $prompt = null)
	{
		if ($prompt === null)
		{
			$prompt = new script\prompt();
		}

		$this->prompt = $prompt->setOutputWriter($this->writer);

		return $this;
	}

	public function getPrompt()
	{
		return $this->prompt;
	}

	public function setLocale(atoum\locale $locale = null)
	{
		$this->locale = $locale ?: new atoum\locale();

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function runAgain()
	{
		return ($this->prompt($this->locale->_('Press <Enter> to reexecute, press any other key and <Enter> to stop...')) == '');
	}

	private function prompt($message)
	{
		return trim($this->prompt->ask(rtrim($message)));
	}
}
