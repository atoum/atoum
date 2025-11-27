<?php

namespace atoum\atoum\asserters;

use atoum\atoum;
use atoum\atoum\asserter;
use atoum\atoum\test;

class error extends asserter
{
    protected ?test\score $score = null;
    protected ?string $message = null;
    protected ?int $type = null;
    protected bool $messageIsPattern = false;

    public function __construct(?asserter\generator $generator = null, ?test\score $score = null, ?atoum\locale $locale = null)
    {
        parent::__construct($generator, null, $locale);

        $this->setScore($score);
    }

    public function __get(string $asserter): mixed
    {
        switch (strtolower($asserter)) {
            case 'exists':
            case 'notexists':
            case 'withanytype':
            case 'withanymessage':
                return $this->{$asserter}();

            default:
                return parent::__get($asserter);
        }
    }

    public function setWithTest(test $test): static
    {
        $this->setScore($test->getScore());

        return parent::setWithTest($test);
    }

    public function setWith(mixed $message = null, ?int $type = null): static
    {
        $message = $message === null || is_string($message) ? $message : (string) $message;

        return $this
            ->withType($type)
            ->withMessage($message)
        ;
    }

    public function setScore(?test\score $score = null): static
    {
        $this->score = $score ?: new test\score();

        return $this;
    }

    public function getScore(): test\score
    {
        return $this->score;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function exists(): static
    {
        $key = $this->score->errorExists($this->message, $this->type, $this->messageIsPattern);

        if ($key !== null) {
            $this->score->deleteError($key);
            $this->pass();
        } else {
            $this->fail($this->getFailMessage(true));
        }

        return $this;
    }

    public function notExists(): static
    {
        $key = $this->getScore()->errorExists($this->message, $this->type, $this->messageIsPattern);

        if ($key === null) {
            $this->pass();
        } else {
            $this->fail($this->getFailMessage());
        }

        return $this;
    }

    public function withType(?int $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function withAnyType(): static
    {
        $this->type = null;

        return $this;
    }

    public function messageIsPattern(): bool
    {
        return $this->messageIsPattern;
    }

    public function withMessage(?string $message): static
    {
        $this->message = $message;
        $this->messageIsPattern = false;

        return $this;
    }

    public function withPattern(?string $pattern): static
    {
        $this->message = $pattern;
        $this->messageIsPattern = true;

        return $this;
    }

    public function withAnyMessage(): static
    {
        $this->message = null;
        $this->messageIsPattern = false;

        return $this;
    }

    public static function getAsString(mixed $errorType): string
    {
        if ($errorType === null) {
            return 'UNKNOWN';
        }

        if (!is_int($errorType)) {
            if (is_string($errorType) && strtolower($errorType) === 'unknown error') {
                return 'UNKNOWN';
            }

            if (is_numeric($errorType)) {
                $errorType = (int) $errorType;
            } else {
                return (string) $errorType;
            }
        }

        switch ($errorType) {
            case E_ERROR:
                return 'E_ERROR';

            case E_WARNING:
                return 'E_WARNING';

            case E_PARSE:
                return 'E_PARSE';

            case E_NOTICE:
                return 'E_NOTICE';

            case E_CORE_ERROR:
                return 'E_CORE_ERROR';

            case E_CORE_WARNING:
                return 'E_CORE_WARNING';

            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';

            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';

            case E_USER_ERROR:
                return 'E_USER_ERROR';

            case E_USER_WARNING:
                return 'E_USER_WARNING';

            case E_USER_NOTICE:
                return 'E_USER_NOTICE';

            case 2048: // E_STRICT is deprecated since PHP 8.4
                return 'E_STRICT';

            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';

            case E_DEPRECATED:
                return 'E_DEPRECATED';

            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';

            case E_ALL:
                return 'E_ALL';

            default:
                return 'UNKNOWN';
        }
    }

    private function getFailMessage(bool $negative = false): string
    {
        $verb = $negative ? 'does not exist' : 'exists';

        switch (true) {
            case $this->type === null && $this->message === null:
                return $this->_('error %s', $verb);

            case $this->type === null && $this->message !== null:
                return $this->_('error with message \'%s\' %s', $this->message, $verb);

            case $this->type !== null && $this->message === null:
                return $this->_('error of type %s %s', self::getAsString($this->type), $verb);

            default:
                return $this->_('error of type %s with message \'%s\' %s', self::getAsString($this->type), $this->message, $verb);
        }
    }
}
