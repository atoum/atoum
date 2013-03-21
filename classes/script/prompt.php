<?php

namespace mageekguy\atoum\script;

use
	mageekguy\atoum,
	mageekguy\atoum\writers,
	mageekguy\atoum\exceptions
;

class prompt
{
	protected $adapter = null;
	protected $outputWriter = null;

	public function __construct(atoum\adapter $adapter = null)
	{
		$this
			->setAdapter($adapter)
			->setOutputWriter()
		;
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new atoum\adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setOutputWriter(atoum\writer $writer = null)
	{
		$this->outputWriter = $writer ?: new writers\std\out();

		return $this;
	}

	public function getOutputWriter()
	{
		return $this->outputWriter;
	}

	public function get($message)
	{
		$this->outputWriter->write(rtrim($message));

		return trim($this->adapter->fgets(STDIN));
	}

	public function select($message, array $choices, $default = null)
	{
		if(!sizeof($choices))
		{
			throw new exceptions\runtime('You must specify at least one choice to use \'' . __METHOD__ . '\'');
		}

		$message .= ' (' . implode($choices, '/') . ')';

		if($default !== null)
		{
			$message .= ' [' . $default . ']';
		}

		$message .= ' ';

		do
		{
			$choice = $this->get($message);
		}
		while(!(in_array($choice, $choices) || $default !== null && $choice === ''));

		return $choice === '' ? (string) $default : $choice;
	}
}
