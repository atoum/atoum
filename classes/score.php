<?php

namespace atoum\atoum;

class score
{
    protected int $passNumber = 0;
    protected array $failAssertions = [];
    protected array $exceptions = [];
    protected array $runtimeExceptions = [];
    protected array $errors = [];
    protected array $outputs = [];
    protected array $durations = [];
    protected array $memoryUsages = [];
    protected array $voidMethods = [];
    protected array $uncompletedMethods = [];
    protected array $skippedMethods = [];
    protected ?score\coverage $coverage = null;

    private static int $failId = 0;

    public function __construct(?score\coverage $coverage = null)
    {
        $this->setCoverage($coverage);
    }

    public function setCoverage(?score\coverage $coverage = null): static
    {
        $this->coverage = $coverage ?: new score\coverage();

        return $this;
    }

    public function getCoverage(): score\coverage
    {
        return $this->coverage;
    }

    public function reset(): static
    {
        $this->passNumber = 0;
        $this->failAssertions = [];
        $this->exceptions = [];
        $this->runtimeExceptions = [];
        $this->errors = [];
        $this->outputs = [];
        $this->durations = [];
        $this->memoryUsages = [];
        $this->uncompletedMethods = [];
        $this->coverage->reset();

        return $this;
    }

    public function getAssertionNumber(): int
    {
        return ($this->passNumber + count($this->failAssertions));
    }

    public function getPassNumber(): int
    {
        return $this->passNumber;
    }

    public function getRuntimeExceptions(): array
    {
        return $this->runtimeExceptions;
    }

    public function getVoidMethods(): array
    {
        return $this->voidMethods;
    }

    public function getLastVoidMethod(): ?array
    {
        return end($this->voidMethods) ?: null;
    }

    public function getVoidMethodNumber(): int
    {
        return count($this->voidMethods);
    }

    public function getUncompletedMethods(): array
    {
        return $this->uncompletedMethods;
    }

    public function getUncompletedMethodNumber(): int
    {
        return count($this->uncompletedMethods);
    }

    public function getLastUncompleteMethod(): ?array
    {
        return end($this->uncompletedMethods) ?: null;
    }

    public function getSkippedMethods(): array
    {
        return $this->skippedMethods;
    }

    public function getLastSkippedMethod(): ?array
    {
        return end($this->skippedMethods) ?: null;
    }

    public function getSkippedMethodNumber(): int
    {
        return count($this->skippedMethods);
    }

    public function getOutputs(): array
    {
        return array_values($this->outputs);
    }

    public function getOutputNumber(): int
    {
        return count($this->outputs);
    }

    public function getTotalDuration(): float
    {
        $total = 0.0;

        foreach ($this->durations as $duration) {
            $total += $duration['value'];
        }

        return $total;
    }

    public function getDurations(): array
    {
        return array_values($this->durations);
    }

    public function getDurationNumber(): int
    {
        return count($this->durations);
    }

    public function getTotalMemoryUsage(): int
    {
        $total = 0;

        foreach ($this->memoryUsages as $memoryUsage) {
            $total += (int) $memoryUsage['value'];
        }

        return $total;
    }

    public function getMemoryUsages(): array
    {
        return array_values($this->memoryUsages);
    }

    public function getMemoryUsageNumber(): int
    {
        return count($this->memoryUsages);
    }

    public function getFailAssertions(): array
    {
        return self::sort(self::cleanAssertions($this->failAssertions));
    }

    public function getLastFailAssertion(): ?array
    {
        $lastFailAssertion = end($this->failAssertions) ?: null;

        if ($lastFailAssertion !== null) {
            $lastFailAssertion = self::cleanAssertion($lastFailAssertion);
        }

        return $lastFailAssertion;
    }

    public function getFailNumber(): int
    {
        return count($this->getFailAssertions());
    }

    public function getErrors(): array
    {
        return self::sort($this->errors);
    }

    public function getErrorNumber(): int
    {
        return count($this->errors);
    }

    public function getExceptions(): array
    {
        return self::sort($this->exceptions);
    }

    public function getExceptionNumber(): int
    {
        return count($this->exceptions);
    }

    public function getRuntimeExceptionNumber(): int
    {
        return count($this->runtimeExceptions);
    }

    public function getMethodsWithFail(): array
    {
        return self::getMethods($this->getFailAssertions());
    }

    public function getMethodsWithError(): array
    {
        return self::getMethods($this->getErrors());
    }

    public function getMethodsWithException(): array
    {
        return self::getMethods($this->getExceptions());
    }

    public function getMethodsNotCompleted(): array
    {
        return self::getMethods($this->getUncompletedMethods());
    }

    public function addPass(): static
    {
        $this->passNumber++;

        return $this;
    }

    public function getLastErroredMethod(): ?array
    {
        return end($this->errors) ?: null;
    }

    public function getLastException(): ?array
    {
        return end($this->exceptions) ?: null;
    }

    public function getLastRuntimeException(): ?exceptions\runtime
    {
        return end($this->runtimeExceptions) ?: null;
    }

    public function addFail(string $file, string $class, ?string $method, ?int $line, string $asserter, string $reason, ?string $case = null, mixed $dataSetKey = null, ?string $dataSetProvider = null): int
    {
        $this->failAssertions[] = [
            'id' => ++self::$failId,
            'case' => $case,
            'dataSetKey' => $dataSetKey,
            'dataSetProvider' => $dataSetProvider,
            'class' => $class,
            'method' => $method,
            'file' => $file,
            'line' => $line,
            'asserter' => $asserter,
            'fail' => $reason
        ];

        return self::$failId;
    }

    public function addException(string $file, string $class, string $method, int|string $line, \exception $exception, ?string $case = null, mixed $dataSetKey = null, ?string $dataSetProvider = null): static
    {
        $this->exceptions[] = [
            'case' => $case,
            'dataSetKey' => $dataSetKey,
            'dataSetProvider' => $dataSetProvider,
            'class' => $class,
            'method' => $method,
            'file' => $file,
            'line' => is_numeric($line) ? (int) $line : $line,
            'value' => (string) $exception
        ];

        return $this;
    }

    public function addRuntimeException(string $file, string $class, string $method, exceptions\runtime $exception): static
    {
        $this->runtimeExceptions[] = $exception;

        return $this;
    }

    public function addError(string $file, string $class, ?string $method, int $line, int|string $type, string $message, ?string $errorFile = null, ?int $errorLine = null, ?string $case = null, mixed $dataSetKey = null, ?string $dataSetProvider = null): static
    {
        $this->errors[] = [
            'case' => $case,
            'dataSetKey' => $dataSetKey,
            'dataSetProvider' => $dataSetProvider,
            'class' => $class,
            'method' => $method,
            'file' => $file,
            'line' => $line,
            'type' => $type,
            'message' => trim($message),
            'errorFile' => $errorFile,
            'errorLine' => $errorLine
        ];

        return $this;
    }

    public function addOutput(string $file, string $class, string $method, string $output): static
    {
        if ($output != '') {
            $this->outputs[] = [
                'class' => $class,
                'method' => $method,
                'value' => $output
            ];
        }

        return $this;
    }

    public function addDuration(string $file, string $class, string $method, float $duration): static
    {
        if ($duration > 0) {
            $this->durations[] = [
                'class' => $class,
                'method' => $method,
                'value' => $duration,
                'path' => $file
            ];
        }

        return $this;
    }

    public function addMemoryUsage(string $file, string $class, string $method, int $memoryUsage): static
    {
        if ($memoryUsage > 0) {
            $this->memoryUsages[] = [
                'class' => $class,
                'method' => $method,
                'value' => $memoryUsage
            ];
        }

        return $this;
    }

    public function addVoidMethod(string $file, string $class, string $method): static
    {
        $this->voidMethods[] = [
            'file' => $file,
            'class' => $class,
            'method' => $method
        ];

        return $this;
    }

    public function addUncompletedMethod(string $file, string $class, string $method, mixed $exitCode, string $output): static
    {
        $this->uncompletedMethods[] = [
            'file' => $file,
            'class' => $class,
            'method' => $method,
            'exitCode' => $exitCode,
            'output' => $output
        ];

        return $this;
    }

    public function addSkippedMethod(?string $file, string $class, string $method, ?int $line, string $message): static
    {
        $this->skippedMethods[] = [
            'file' => $file,
            'class' => $class,
            'method' => $method,
            'line' => $line,
            'message' => $message
        ];

        return $this;
    }

    public function merge(self $score): static
    {
        $this->passNumber += $score->getPassNumber();
        $this->failAssertions = array_merge($this->failAssertions, $score->failAssertions);
        $this->exceptions = array_merge($this->exceptions, $score->exceptions);
        $this->runtimeExceptions = array_merge($this->runtimeExceptions, $score->runtimeExceptions);
        $this->errors = array_merge($this->errors, $score->errors);
        $this->outputs = array_merge($this->outputs, $score->outputs);
        $this->durations = array_merge($this->durations, $score->durations);
        $this->memoryUsages = array_merge($this->memoryUsages, $score->memoryUsages);
        $this->voidMethods = array_merge($this->voidMethods, $score->voidMethods);
        $this->uncompletedMethods = array_merge($this->uncompletedMethods, $score->uncompletedMethods);
        $this->skippedMethods = array_merge($this->skippedMethods, $score->skippedMethods);
        $this->coverage->merge($score->coverage);

        return $this;
    }

    public function errorExists(?string $message = null, ?int $type = null, bool $messageIsPattern = false): ?int
    {
        $messageIsNull = $message === null;
        $typeIsNull = $type === null;

        foreach ($this->errors as $key => $error) {
            $messageMatch = $messageIsNull === true ? true : ($messageIsPattern == false ? $message == $error['message'] : preg_match($message, $error['message']) == 1);
            $typeMatch = $typeIsNull === true ? true : $error['type'] == $type;

            if ($messageMatch === true && $typeMatch === true) {
                return $key;
            }
        }

        return null;
    }

    public function deleteError(int $key): static
    {
        if (isset($this->errors[$key]) === false) {
            throw new exceptions\logic\invalidArgument('Error key \'' . $key . '\' does not exist');
        }

        unset($this->errors[$key]);

        return $this;
    }

    public function failExists(asserter\exception $exception): bool
    {
        $id = $exception->getCode();

        return (count(array_filter($this->failAssertions, function ($assertion) use ($id) {
            return ($assertion['id'] === $id);
        })) > 0);
    }

    private static function getMethods(array $array): array
    {
        $methods = [];

        foreach ($array as $value) {
            if (isset($methods[$value['class']]) === false || in_array($value['method'], $methods[$value['class']]) === false) {
                $methods[$value['class']][] = $value['method'];
            }
        }

        return $methods;
    }

    private static function cleanAssertions(array $assertions): array
    {
        return array_map([__CLASS__, 'cleanAssertion'], array_values($assertions));
    }

    private static function cleanAssertion(array $assertion): array
    {
        unset($assertion['id']);

        return $assertion;
    }

    private static function sort(array $array): array
    {
        usort(
            $array,
            function ($a, $b) {
                if ($a['file'] !== $b['file']) {
                    return strcmp($a['file'], $b['file']);
                } elseif ($a['line'] === $b['line']) {
                    return 0;
                } else {
                    return ($a['line'] < $b['line'] ? -1 : 1);
                }
            }
        );

        return $array;
    }
}
