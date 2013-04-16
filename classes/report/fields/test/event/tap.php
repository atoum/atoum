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
	protected $testPoint = 0;
	protected $testLine = '';

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

	public function __toString()
	{
		return $this->testLine;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		$eventHandled = parent::handleEvent($event, $observable);

		if ($eventHandled === true)
		{
			switch ($this->event)
			{
				case runner::runStart:
					$this->testPoint = 0;
					$this->testLine = '';
					break;

				case test::success:
					$this->testLine = 'ok ' . ++$this->testPoint . PHP_EOL;
					break;

				case test::error:
				case test::exception:
				case test::runtimeException:
				case test::uncompleted:
					break;

				case test::fail:
					$lastFailAssertion = $observable->getScore()->getLastFailAssertion();
					$this->testLine = 'not ok ' . ++$this->testPoint . ' - ' . trim($lastFailAssertion['class']) . '::' . trim($lastFailAssertion['method']) . '()' . PHP_EOL . '# ' . str_replace(PHP_EOL, PHP_EOL . '# ', trim($lastFailAssertion['fail'])) . PHP_EOL;
                    $this->testLine .= '# ' . $lastFailAssertion['file'] . ':' . $lastFailAssertion['line'] . PHP_EOL;
					break;

				case test::void:
					$lastVoidMethod = $observable->getScore()->getLastVoidMethod();
					$this->testLine = 'not ok ' . ++$this->testPoint . ' # TODO ' . trim($lastVoidMethod['class']) . '::' . trim($lastVoidMethod['method']) . '()' . PHP_EOL;
                    $this->testLine .= '# ' . $lastVoidMethod['file'] . ':' . $lastVoidMethod['line'] . PHP_EOL;
					break;

				case test::skipped:
					$lastSkippedMethod = $observable->getScore()->getLastSkippedMethod();
					$this->testLine = 'ok ' . ++$this->testPoint . ' # SKIP ' . trim($lastSkippedMethod['class']) . '::' . trim($lastSkippedMethod['method']) . '()' . PHP_EOL . '# ' . str_replace(PHP_EOL, PHP_EOL . '# ', trim($lastSkippedMethod['message'])) . PHP_EOL;
					break;
			}
		}

		return $eventHandled;
	}
}
