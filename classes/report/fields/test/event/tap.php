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
					$this->testLine .= '# ' . $observable->getClass() . '::' . $observable->getCurrentMethod() . '()' . PHP_EOL;
					break;

				case test::error:
					$lastError = $observable->getScore()->getLastErroredMethod();

					$this->testLine = 'not ok ' . ++$this->testPoint . ' - ' . trim($lastError['class']) . '::' . trim($lastError['method']) . '()' . PHP_EOL;
					$this->testLine .= $this->renderErrors($observable, trim($lastError['class']), trim($lastError['method']));
					break;

				case test::fail:
					$lastFailAssertion = $observable->getScore()->getLastFailAssertion();
					$this->testLine = 'not ok ' . ++$this->testPoint . ' - ' . trim($lastFailAssertion['class']) . '::' . trim($lastFailAssertion['method']) . '()' . PHP_EOL . '# ' . str_replace(PHP_EOL, PHP_EOL . '# ', trim($lastFailAssertion['fail'])) . PHP_EOL;
					$this->testLine .= '# ' . $lastFailAssertion['file'] . ':' . $lastFailAssertion['line'] . PHP_EOL;
					$this->testLine .= $this->renderErrors($observable, trim($lastFailAssertion['class']), trim($lastFailAssertion['method']));
					break;

				case test::void:
					$lastVoidMethod = $observable->getScore()->getLastVoidMethod();
					$this->testLine = 'not ok ' . ++$this->testPoint . ' # TODO ' . trim($lastVoidMethod['class']) . '::' . trim($lastVoidMethod['method']) . '()' . PHP_EOL;
					$this->testLine .= '# ' . $lastVoidMethod['file'] . PHP_EOL;
					$this->testLine .= $this->renderErrors($observable, trim($lastVoidMethod['class']), trim($lastVoidMethod['method']));
					break;

				case test::uncompleted:
					$lastUncompleteMethod = $observable->getScore()->getLastUncompleteMethod();
					$lastError = $observable->getScore()->getLastErroredMethod();

					$this->testLine = 'not ok ' . ++$this->testPoint . ' - ' . trim($lastUncompleteMethod['class']) . '::' . trim($lastUncompleteMethod['method']) . '()' . PHP_EOL;

					if ($lastError['class'] === $lastUncompleteMethod['class'] && $lastError['method'] === $lastUncompleteMethod['method'])
					{
						$this->testLine .= '# ' . $lastError['type'] . ' : ' . str_replace(PHP_EOL, PHP_EOL . '# ', trim($lastError['message'])) . PHP_EOL;
					}
					else
					{
						$this->testLine .= '# ' . str_replace(PHP_EOL, PHP_EOL . '# ', trim($lastUncompleteMethod['output']) ?: 'uncomplete method') . PHP_EOL;
					}

					$this->testLine .= '# ' . $lastUncompleteMethod['file'] . PHP_EOL;
					break;

				case test::skipped:
					$lastSkippedMethod = $observable->getScore()->getLastSkippedMethod();
					$this->testLine = 'ok ' . ++$this->testPoint . ' # SKIP ' . trim($lastSkippedMethod['class']) . '::' . trim($lastSkippedMethod['method']) . '()' . PHP_EOL . '# ' . str_replace(PHP_EOL, PHP_EOL . '# ', trim($lastSkippedMethod['message'])) . PHP_EOL;
					$this->testLine .= '# ' . $lastSkippedMethod['file'] . ':' . $lastSkippedMethod['line'] . PHP_EOL;
					break;

				case test::exception:
					$lastException = $observable->getScore()->getLastException();
					$this->testLine = 'not ok ' . ++$this->testPoint . ' - ' . trim($lastException['class']) . '::' . trim($lastException['method']) . '()' . PHP_EOL . '# ' . str_replace(PHP_EOL, PHP_EOL . '# ', trim($lastException['value'])) . PHP_EOL;
					$this->testLine .= '# ' . $lastException['file'] . ':' . $lastException['line'] . PHP_EOL;
					break;

				case test::runtimeException:
					$lastRuntimeException = $observable->getScore()->getLastRuntimeException();
					$this->testLine = 'Bail out!' . ($lastRuntimeException->getMessage() ? ' ' . trim($lastRuntimeException->getMessage()) : '') . PHP_EOL;
					break;
			}
		}

		return $eventHandled;
	}

	private function renderErrors(atoum\observable $observable, $class, $method)
	{
		$errors = '';

		foreach ($observable->getScore()->getErrors() as $error) if ($error['class'] === $class && $error['method'] === $method)
		{
			$errorType = atoum\asserters\error::getAsString($error['type']);

			$errors .= '# ' . $errorType . ' : ' . str_replace(PHP_EOL, PHP_EOL . '# ', trim($error['message'])) . PHP_EOL;
			$errors .= '# ' . (isset($error['errorFile']) ? $error['errorFile'] : $error['file']) . ':' . (isset($error['errorLine']) ? $error['errorLine'] : $error['line']) . PHP_EOL;
		}

		return $errors;
	}
}
