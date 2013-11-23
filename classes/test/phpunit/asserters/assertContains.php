<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter,
	mageekguy\atoum\test\phpunit\asserter\selector
;

class assertContains extends asserter
{
	protected $asserterSelector;

	public function __construct(atoum\asserter\generator $generator = null)
	{
		parent::__construct($generator);

		$this->setAsserterSelector();
	}

	public function setAsserterSelector(selector $selector = null)
	{
		$this->asserterSelector = $selector ?: new selector\containment();
	}

	public function getAsserterSelector()
	{
		return $this->asserterSelector;
	}

	public function setWithArguments(array $arguments)
	{
		parent::setWithArguments($arguments);

		try
		{
			$asserter = $this->asserterSelector->selectFor($arguments[1]);
		}
		catch(atoum\asserter\exception $exception)
		{
			throw new atoum\exceptions\logic\invalidArgument(sprintf('Cannot check containment in object(%s)', get_class($arguments[1])));
		}

		try
		{
			if (is_object($arguments[0]))
			{
				$asserter->strictlyContains($arguments[0]);
			}
			else
			{
				$asserter->contains($arguments[0]);
			}

			$this->pass();
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[2]) ? $arguments[2] : sprintf('%s does not contain %s', $this->getTypeOf($arguments[1]), $this->getTypeOf($arguments[0])));
		}

		return $this;
	}
} 