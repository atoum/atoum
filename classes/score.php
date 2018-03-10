<?php

namespace mageekguy\atoum;

class score
{
    protected $passNumber = 0;
    protected $failAssertions = [];
    protected $exceptions = [];
    protected $runtimeExceptions = [];
    protected $errors = [];
    protected $outputs = [];
    protected $durations = [];
    protected $memoryUsages = [];
    protected $voidMethods = [];
    protected $uncompletedMethods = [];
    protected $skippedMethods = [];
    protected $coverage = null;

    private static $failId = 0;

    public function __construct(score\coverage $coverage = null)
    {
        $this->setCoverage($coverage);
    }

    public function setCoverage(score\coverage $coverage = null)
    {
        $this->coverage = $coverage ?: new score\coverage();

        return $this;
    }

    public function getCoverage()
    {
        return $this->coverage;
    }

    public function reset()
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

    public function getAssertionNumber()
    {
        return ($this->passNumber + count($this->failAssertions));
    }

    public function getPassNumber()
    {
        return $this->passNumber;
    }

    public function getRuntimeExceptions()
    {
        return $this->runtimeExceptions;
    }

    public function getVoidMethods()
    {
        return $this->voidMethods;
    }

    public function getLastVoidMethod()
    {
        return end($this->voidMethods) ?: null;
    }

    public function getVoidMethodNumber()
    {
        return count($this->voidMethods);
    }

    public function getUncompletedMethods()
    {
        return $this->uncompletedMethods;
    }

    public function getUncompletedMethodNumber()
    {
        return count($this->uncompletedMethods);
    }

    public function getLastUncompleteMethod()
    {
        return end($this->uncompletedMethods) ?: null;
    }

    public function getSkippedMethods()
    {
        return $this->skippedMethods;
    }

    public function getLastSkippedMethod()
    {
        return end($this->skippedMethods) ?: null;
    }

    public function getSkippedMethodNumber()
    {
        return count($this->skippedMethods);
    }

    public function getOutputs()
    {
        return array_values($this->outputs);
    }

    public function getOutputNumber()
    {
        return count($this->outputs);
    }

    public function getTotalDuration()
    {
        $total = 0.0;

        foreach ($this->durations as $duration) {
            $total += $duration['value'];
        }

        return $total;
    }

    public function getDurations()
    {
        return array_values($this->durations);
    }

    public function getDurationNumber()
    {
        return count($this->durations);
    }

    public function getTotalMemoryUsage()
    {
        $total = 0.0;

        foreach ($this->memoryUsages as $memoryUsage) {
            $total += $memoryUsage['value'];
        }

        return $total;
    }

    public function getMemoryUsages()
    {
        return array_values($this->memoryUsages);
    }

    public function getMemoryUsageNumber()
    {
        return count($this->memoryUsages);
    }

    public function getFailAssertions()
    {
        return self::sort(self::cleanAssertions($this->failAssertions));
    }

    public function getLastFailAssertion()
    {
        $lastFailAssertion = end($this->failAssertions) ?: null;

        if ($lastFailAssertion !== null) {
            $lastFailAssertion = self::cleanAssertion($lastFailAssertion);
        }

        return $lastFailAssertion;
    }

    public function getFailNumber()
    {
        return count($this->getFailAssertions());
    }

    public function getErrors()
    {
        return self::sort($this->errors);
    }

    public function getErrorNumber()
    {
        return count($this->errors);
    }

    public function getExceptions()
    {
        return self::sort($this->exceptions);
    }

    public function getExceptionNumber()
    {
        return count($this->exceptions);
    }

    public function getRuntimeExceptionNumber()
    {
        return count($this->runtimeExceptions);
    }

    public function getMethodsWithFail()
    {
        return self::getMethods($this->getFailAssertions());
    }

    public function getMethodsWithError()
    {
        return self::getMethods($this->getErrors());
    }

    public function getMethodsWithException()
    {
        return self::getMethods($this->getExceptions());
    }

    public function getMethodsNotCompleted()
    {
        return self::getMethods($this->getUncompletedMethods());
    }

    public function addPass()
    {
        $this->passNumber++;

        return $this;
    }

    public function getLastErroredMethod()
    {
        return end($this->errors) ?: null;
    }

    public function getLastException()
    {
        return end($this->exceptions) ?: null;
    }

    public function getLastRuntimeException()
    {
        return end($this->runtimeExceptions) ?: null;
    }

    public function addFail($file, $class, $method, $line, $asserter, $reason, $case = null, $dataSetKey = null, $dataSetProvider = null)
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

    public function addException($file, $class, $method, $line, \exception $exception, $case = null, $dataSetKey = null, $dataSetProvider = null)
    {
        $this->exceptions[] = [
            'case' => $case,
            'dataSetKey' => $dataSetKey,
            'dataSetProvider' => $dataSetProvider,
            'class' => $class,
            'method' => $method,
            'file' => $file,
            'line' => $line,
            'value' => (string) $exception
        ];

        return $this;
    }

    public function addRuntimeException($file, $class, $method, exceptions\runtime $exception)
    {
        $this->runtimeExceptions[] = $exception;

        return $this;
    }

    public function addError($file, $class, $method, $line, $type, $message, $errorFile = null, $errorLine = null, $case = null, $dataSetKey = null, $dataSetProvider = null)
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

    public function addOutput($file, $class, $method, $output)
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

    public function addDuration($file, $class, $method, $duration)
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

    public function addMemoryUsage($file, $class, $method, $memoryUsage)
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

    public function addVoidMethod($file, $class, $method)
    {
        $this->voidMethods[] = [
            'file' => $file,
            'class' => $class,
            'method' => $method
        ];

        return $this;
    }

    public function addUncompletedMethod($file, $class, $method, $exitCode, $output)
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

    public function addSkippedMethod($file, $class, $method, $line, $message)
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

    public function merge(self $score)
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

    public function errorExists($message = null, $type = null, $messageIsPattern = false)
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

    public function deleteError($key)
    {
        if (isset($this->errors[$key]) === false) {
            throw new exceptions\logic\invalidArgument('Error key \'' . $key . '\' does not exist');
        }

        unset($this->errors[$key]);

        return $this;
    }

    public function failExists(asserter\exception $exception)
    {
        $id = $exception->getCode();

        return (count(array_filter($this->failAssertions, function ($assertion) use ($id) {
            return ($assertion['id'] === $id);
        })) > 0);
    }

    private static function getMethods(array $array)
    {
        $methods = [];

        foreach ($array as $value) {
            if (isset($methods[$value['class']]) === false || in_array($value['method'], $methods[$value['class']]) === false) {
                $methods[$value['class']][] = $value['method'];
            }
        }

        return $methods;
    }

    private static function cleanAssertions(array $assertions)
    {
        return array_map([__CLASS__, 'cleanAssertion'], array_values($assertions));
    }

    private static function cleanAssertion(array $assertion)
    {
        unset($assertion['id']);

        return $assertion;
    }

    private static function sort(array $array)
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
