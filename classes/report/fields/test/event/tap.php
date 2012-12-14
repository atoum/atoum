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
					$lastFailure = current(array_slice($observable->getScore()->getFailAssertions(), -1));
					$this->testPointNumber++;
					$this->description = ($lastFailure['class'] === null || $lastFailure['method'] === null ? '' : trim($lastFailure['class']) . '::' . trim($lastFailure['method']) . '()');

					if ($lastFailure['fail'] !== null)
					{
						$this->description .= PHP_EOL . trim($lastFailure['fail']);
					}
					break;

				case test::void:
					$lastVoidMethod = current(array_slice($observable->getScore()->getVoidMethods(), -1));
					$this->testPointNumber++;
					$this->description = ($lastVoidMethod['class'] === null || $lastVoidMethod['method'] === null ? '' : trim($lastVoidMethod['class']) . '::' . trim($lastVoidMethod['method']) . '()');
					break;

				case test::skipped:
					$lastSkippedMethod = current(array_slice($observable->getScore()->getSkippedMethods(), -1));
					$this->testPointNumber++;
					$this->description = ($lastSkippedMethod['class'] === null || $lastSkippedMethod['method'] === null ? '' : trim($lastSkippedMethod['class']) . '::' . trim($lastSkippedMethod['method']) . '()');

					if ($lastSkippedMethod['message'] !== null)
					{
						$this->description .= PHP_EOL . trim($lastSkippedMethod['message']);
					}
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
				case test::success:
					$string = 'ok ' . $this->testPointNumber . PHP_EOL;
					break;

				case test::fail:
				case test::error:
				case test::exception:
				case test::runtimeException:
				case test::uncompleted:
					$string = 'not ok ' . $this->testPointNumber . $this->descriptionToString('-') . PHP_EOL;
					break;

				case test::void:
					$string = 'not ok ' . $this->testPointNumber . $this->descriptionToString('# TODO', true) . PHP_EOL;
					break;

				case test::skipped:
					$string = 'ok ' . $this->testPointNumber . $this->descriptionToString('# SKIP', true) . PHP_EOL;
					break;
			}
		}

		return $string;
	}

	protected function descriptionToString($prefix = '', $mandatoryPrefix = false)
	{
		$description = '';

		$prefix = trim($prefix);

		if ($prefix != '')
		{
			if ($this->description != '' || $mandatoryPrefix === true)
			{
				$description .= ' ' . $prefix;
			}
		}

		if ($this->description != '')
		{
			if ($prefix !== '')
			{
				$description .= ' ';
			}

			$description .= join(PHP_EOL . '# ', explode(PHP_EOL, $this->description));
		}

		return $description;
	}
}
