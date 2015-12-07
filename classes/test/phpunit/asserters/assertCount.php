<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter,
	mageekguy\atoum\test\phpunit\asserter\selector
;

class assertCount extends asserter
{
	protected $asserterSelector;

	public function __construct(asserter\generator $generator = null)
	{
		parent::__construct($generator);

		$this->setAsserterSelector();
	}

	public function setAsserterSelector(selector $selector = null)
	{
		$this->asserterSelector = $selector ?: new selector\size();
	}

	public function getAsserterSelector()
	{
		return $this->asserterSelector;
	}

	public function setWithArguments(array $arguments)
	{
		parent::setWithArguments($arguments);

		$asserter = $this->asserterSelector->selectFor($arguments[1]);

		try
		{
			$asserter->hasSize($arguments[0]);

			$this->pass();
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[2]) ? $arguments[2] : $exception->getMessage());
		}

		return $this;
	}
} 