<?php

namespace mageekguy\atoum\asserters\adapter\call;

use
	mageekguy\atoum\asserters\adapter
;

class manager
{
	protected $calls = null;

	public function __construct()
	{
		$this->calls = new \splObjectStorage();
	}

	public function add(adapter\call $call)
	{
		$this->calls->attach($call);

		return $this;
	}

	public function remove(adapter\call $call)
	{
		$this->calls->detach($call);

		return $this;
	}

	public function check()
	{
		if (sizeof($this->calls) > 0)
		{
			$this->calls->rewind();

			$call = $this->calls->current();

			throw new manager\exception('Asserter ' . get_class($call) . ' is not evaluated in file \'' . $call->getLastAssertionFile() . '\' on line ' . $call->getLastAssertionLine());
		}

		return $this;
	}
}
