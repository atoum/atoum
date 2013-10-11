<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter,
	mageekguy\atoum\tools\arguments,
	mageekguy\atoum\asserters\adapter\exceptions
;

class adapter extends adapter\call
{
	public function call($function)
	{
		return $this->setFunction($function);
	}

	public function withArguments()
	{
		return $this->setArguments(func_get_args());
	}

	public function withIdenticalArguments()
	{
		return $this->setIdenticalArguments(func_get_args());
	}

	public function withAtLeastArguments(array $arguments)
	{
		return $this->setArguments($arguments);
	}

	public function withAtLeastIdenticalArguments(array $arguments)
	{
		return $this->setIdenticalArguments($arguments);
	}

	public function withAnyArguments()
	{
		return $this->unsetArguments();
	}

	public function withoutAnyArgument()
	{
		return $this->withAtLeastArguments(array());
	}
}
