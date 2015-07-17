<?php

namespace mageekguy\atoum\report\fields\runner\failures;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\adapter,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner
;

class execute extends runner\failures
{
	protected $command = '';
	protected $adapter;

	public function __construct($command)
	{
		parent::__construct();

		$this
			->setCommand($command)
			->setAdapter()
		;
	}

	public function __toString()
	{
		if ($this->runner !== null)
		{
			$fails = array();

			foreach ($this->runner->getScore()->getFailAssertions() as $fail)
			{
				switch (true)
				{
					case isset($fails[$fail['file']]) === false:
					case $fails[$fail['file']] > $fail['line']:
						$fails[$fail['file']] = $fail['line'];
				}
			}

			ksort($fails);

			foreach ($fails as $file => $line)
			{
				$this->adapter->system(sprintf($this->getCommand(), $file, $line));
			}
		}

		return '';
	}

	public function setCommand($command)
	{
		$this->command = (string) $command;

		return $this;
	}

	public function getCommand()
	{
		return $this->command;
	}

	public function setAdapter(adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}
}
