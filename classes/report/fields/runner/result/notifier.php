<?php

namespace mageekguy\atoum\report\fields\runner\result;

use
	mageekguy\atoum,
	mageekguy\atoum\adapter,
	mageekguy\atoum\report\fields\runner\result
;

abstract class notifier extends result
{
	private $adapter;

	public function __construct(adapter $adapter = null)
	{
		parent::__construct();

		$this->adapter = $adapter ?: new adapter();
	}

	public function __toString()
	{
		return $this->notify() ?: '';
	}

	public function notify()
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
			$title = 'Failure !';
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

		return $this->send($title, $message, $success);
	}

	public function execute($command, array $args)
	{
		$output = null;
		array_walk($args, function(& $arg) { $arg = escapeshellarg($arg); });
		array_unshift($args, $command);

		$this->getAdapter()->system(call_user_func_array('sprintf', $args));
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	protected abstract function send($title, $message, $success);
}
