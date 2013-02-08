<?php

namespace mageekguy\atoum\report\fields\runner\result;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\result,
	mageekguy\atoum\observable
;

abstract class notifier extends result
{
	public function handleEvent($event, observable $observable)
	{
		if (parent::handleEvent($event, $observable) === false)
		{
			return false;
		}
		else
		{
			if ($this->failNumber === 0 && $this->errorNumber === 0 && $this->exceptionNumber === 0 && $this->uncompletedMethodNumber === 0)
			{
				$success = true;
				$title = 'Success !';
				$message = sprintf(
					$this->locale->_('%s %s %s %s %s'),
					sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber) . PHP_EOL,
					sprintf($this->locale->__('%s/%s method', '%s/%s methods', $this->testMethodNumber), $this->testMethodNumber - $this->voidMethodNumber - $this->skippedMethodNumber, $this->testMethodNumber) . PHP_EOL,
					sprintf($this->locale->__('%s void method', '%s void methods', $this->voidMethodNumber), $this->voidMethodNumber) . PHP_EOL,
					sprintf($this->locale->__('%s skipped method', '%s skipped methods', $this->skippedMethodNumber), $this->skippedMethodNumber) . PHP_EOL,
					sprintf($this->locale->__('%s assertion', '%s assertions', $this->assertionNumber), $this->assertionNumber) . PHP_EOL
				);
			}
			else
			{
				$success = false;
				$title = 'Failure';
				$message = sprintf(
					$this->locale->_('%s %s %s %s %s %s %s %s'),
					sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber) . PHP_EOL,
					sprintf($this->locale->__('%s/%s method', '%s/%s methods', $this->testMethodNumber), $this->testMethodNumber - $this->voidMethodNumber - $this->skippedMethodNumber - $this->uncompletedMethodNumber, $this->testMethodNumber) . PHP_EOL,
					sprintf($this->locale->__('%s void method', '%s void methods', $this->voidMethodNumber), $this->voidMethodNumber) . PHP_EOL,
					sprintf($this->locale->__('%s skipped method', '%s skipped methods', $this->skippedMethodNumber), $this->skippedMethodNumber) . PHP_EOL,
					sprintf($this->locale->__('%s uncompleted method', '%s uncompleted methods', $this->uncompletedMethodNumber), $this->uncompletedMethodNumber) . PHP_EOL,
					sprintf($this->locale->__('%s failure', '%s failures', $this->failNumber), $this->failNumber) . PHP_EOL,
					sprintf($this->locale->__('%s error', '%s errors', $this->errorNumber), $this->errorNumber) . PHP_EOL,
					sprintf($this->locale->__('%s exception', '%s exceptions', $this->exceptionNumber), $this->exceptionNumber) . PHP_EOL
				);
			}

			static::notify($title, $message, $success);

			return true;
		}
	}

	public function __toString()
	{
		return '';
	}

	abstract protected static function notify($title, $message, $success);
}
