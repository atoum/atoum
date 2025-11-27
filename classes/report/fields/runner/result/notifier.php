<?php

namespace atoum\atoum\report\fields\runner\result;

use atoum\atoum;
use atoum\atoum\report\fields\runner\result;

abstract class notifier extends result
{
    protected ?atoum\adapter $adapter = null;

    public function __construct(?atoum\adapter $adapter = null)
    {
        parent::__construct();

        $this->setAdapter($adapter);
    }

    public function __toString(): string
    {
        $string = $this->notify();

        return $string == '' ? '' : trim($string) . PHP_EOL;
    }

    public function notify(): string
    {
        if ($this->success === true) {
            $title = 'Success!';
            $message = sprintf(
                $this->locale->_('%s %s %s %s %s'),
                sprintf($this->locale->__('%s test', '%s tests', $this->testNumber), $this->testNumber) . PHP_EOL,
                sprintf($this->locale->__('%s/%s method', '%s/%s methods', $this->testMethodNumber), $this->testMethodNumber - $this->voidMethodNumber - $this->skippedMethodNumber, $this->testMethodNumber) . PHP_EOL,
                sprintf($this->locale->__('%s void method', '%s void methods', $this->voidMethodNumber), $this->voidMethodNumber) . PHP_EOL,
                sprintf($this->locale->__('%s skipped method', '%s skipped methods', $this->skippedMethodNumber), $this->skippedMethodNumber) . PHP_EOL,
                sprintf($this->locale->__('%s assertion', '%s assertions', $this->assertionNumber), $this->assertionNumber) . PHP_EOL
            );
        } else {
            $title = 'Failure!';
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

        return $this->send($title, $message, $this->success);
    }

    public function setAdapter(?atoum\adapter $adapter = null): static
    {
        $this->adapter = $adapter ?: new atoum\adapter();

        return $this;
    }

    public function getAdapter(): atoum\adapter
    {
        return $this->adapter;
    }

    public function send(string $title, string $message, mixed $success): string
    {
        $result = $this->adapter->system(sprintf(
            $this->getCommand(),
            escapeshellarg($title),
            escapeshellarg($message),
            escapeshellarg($success)
        ));

        return (string) $result;
    }

    abstract protected function getCommand(): string;
}
