<?php

namespace mageekguy\atoum\report\fields\test\event;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\runner,
	mageekguy\atoum\report,
	mageekguy\atoum\exceptions
;

class tap extends report\fields\event
{
	protected $testPointNumber = 0;
	protected $description = '';

	public function __construct()
	{
		parent::__construct(array(
				runner::runStart,
				test::fail,
				test::error,
				test::void,
				test::uncompleted,
				test::skipped,
				test::exception,
				test::runtimeException,
				test::success
			)
		);
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		$eventHandled = parent::handleEvent($event, $observable);

		if ($eventHandled === true)
		{
			switch ($this->event)
			{
				case runner::runStart:
					$this->testPointNumber = 0;
					break;

				case test::success:
					$this->testPointNumber++;
					$this->description = '';
					break;

				case test::fail:
					$lastFailures = current(array_slice($observable->getScore()->getFailAssertions(), -1));
					$this->testPointNumber++;
					$this->description = $lastFailures['fail'];
					break;
			}
		}

		return $eventHandled;
	}

	public function __toString()
	{
		$string = '';

		if ($this->observable !== null)
		{
			switch ($this->event)
			{
				case runner::runStart:
					$this->testPointNumber = 0;
					break;

				case test::success:
					$string = 'ok ' . $this->testPointNumber . PHP_EOL;
					break;

				case test::fail:
				case test::error:
				case test::exception:
				case test::void:
				case test::uncompleted:
				case test::skipped:
					$string = 'not ok ' . $this->testPointNumber . ' - ' . $this->description . PHP_EOL;
					break;
			}
		}

		return $string;
	}
}
