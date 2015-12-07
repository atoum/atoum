<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter
;

abstract class assertArray extends asserter
{
	protected $asserterSelector;

	public function __construct(atoum\asserter\generator $generator = null)
	{
		parent::__construct($generator);

		$this->setAsserterSelector();
	}

	public function setAsserterSelector(asserter\selector $selector = null)
	{
		$this->asserterSelector = $selector ?: new asserter\selector\phpArray();
	}

	public function getAsserterSelector()
	{
		return $this->asserterSelector;
	}
} 