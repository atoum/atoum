<?php

namespace atoum\atoum\report\fields\runner;

use atoum\atoum\observable;
use atoum\atoum\report;
use atoum\atoum\runner;

abstract class result extends report\field
{
    protected bool $success = false;
    protected ?int $testNumber = null;
    protected ?int $testMethodNumber = null;
    protected ?int $assertionNumber = null;
    protected ?int $failNumber = null;
    protected ?int $errorNumber = null;
    protected ?int $exceptionNumber = null;
    protected ?int $voidMethodNumber = null;
    protected ?int $uncompletedMethodNumber = null;
    protected ?int $skippedMethodNumber = null;

    public function __construct()
    {
        parent::__construct([runner::runStop]);
    }

    public function getTestNumber(): ?int
    {
        return $this->testNumber;
    }

    public function getTestMethodNumber(): ?int
    {
        return $this->testMethodNumber;
    }

    public function getAssertionNumber(): ?int
    {
        return $this->assertionNumber;
    }

    public function getFailNumber(): ?int
    {
        return $this->failNumber;
    }

    public function getErrorNumber(): ?int
    {
        return $this->errorNumber;
    }

    public function getExceptionNumber(): ?int
    {
        return $this->exceptionNumber;
    }

    public function getVoidMethodNumber(): ?int
    {
        return $this->voidMethodNumber;
    }

    public function getUncompletedMethodNumber(): ?int
    {
        return $this->uncompletedMethodNumber;
    }

    public function getSkippedMethodNumber(): ?int
    {
        return $this->skippedMethodNumber;
    }

    public function handleEvent(string $event, observable $observable): bool
    {
        if (parent::handleEvent($event, $observable) === false) {
            return false;
        } else {
            $score = $observable->getScore();

            $this->testNumber = $observable->getTestNumber();
            $this->testMethodNumber = $observable->getTestMethodNumber();
            $this->assertionNumber = $score->getAssertionNumber();
            $this->failNumber = $score->getFailNumber();
            $this->errorNumber = $score->getErrorNumber();
            $this->exceptionNumber = $score->getExceptionNumber();
            $this->voidMethodNumber = $score->getVoidMethodNumber();
            $this->uncompletedMethodNumber = $score->getUncompletedMethodNumber();
            $this->skippedMethodNumber = $score->getSkippedMethodNumber();
            $this->success = ($this->failNumber === 0 && $this->errorNumber === 0 && $this->exceptionNumber === 0 && $this->uncompletedMethodNumber === 0);

            if ($observable->shouldFailIfVoidMethods() && $this->voidMethodNumber > 0) {
                $this->success = false;
            }

            if ($observable->shouldFailIfSkippedMethods() && $this->skippedMethodNumber > 0) {
                $this->success = false;
            }

            return true;
        }
    }
}
