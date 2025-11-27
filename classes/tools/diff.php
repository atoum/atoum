<?php

namespace atoum\atoum\tools;

class diff
{
    protected ?string $expected = null;
    protected ?string $actual = null;
    protected ?array $diff = null;
    protected ?diff\decorator $decorator = null;

    public function __construct(mixed $expected = null, mixed $actual = null)
    {
        $this->setDecorator();

        if ($expected !== null) {
            $this->setExpected($expected);
        }

        if ($actual !== null) {
            $this->setActual($actual);
        }
    }

    public function __invoke(mixed $expected = null, mixed $actual = null): static
    {
        $this->make($expected, $actual);

        return $this;
    }

    public function __toString(): string
    {
        return $this->decorator->decorate($this);
    }

    public function setDecorator(?diff\decorator $decorator = null): static
    {
        $this->decorator = $decorator ?: new diff\decorator();
        return $this;
    }

    public function getDecorator(): diff\decorator
    {
        return $this->decorator;
    }

    public function setExpected(mixed $mixed): static
    {
        $this->expected = (string) $mixed;
        $this->diff = null;

        return $this;
    }

    public function getExpected(): ?string
    {
        return $this->expected;
    }

    public function setActual(mixed $mixed): static
    {
        $this->actual = (string) $mixed;
        $this->diff = null;

        return $this;
    }

    public function getActual(): ?string
    {
        return $this->actual;
    }

    public function make(mixed $expected = null, mixed $actual = null): array
    {
        if ($expected !== null) {
            $this->setExpected($expected);
        }

        if ($expected !== null) {
            $this->setActual($actual);
        }

        if ($this->diff === null) {
            $this->diff = $this->diff(self::split($this->expected), self::split($this->actual));
        }

        return $this->diff;
    }

    protected function diff(array $old, array $new): array
    {
        $diff = [];

        if (count($old) > 0 || count($new) > 0) {
            $lengths = [];
            $maxLength = 0;

            foreach ($old as $oldKey => $oldValue) {
                $newKeys = array_keys($new, $oldValue);

                foreach ($newKeys as $newKey) {
                    $lengths[$oldKey][$newKey] = isset($lengths[$oldKey - 1][$newKey - 1]) === false ? 1 : $lengths[$oldKey - 1][$newKey - 1] + 1;

                    if ($lengths[$oldKey][$newKey] > $maxLength) {
                        $maxLength = $lengths[$oldKey][$newKey];
                        $oldMaxLength = $oldKey + 1 - $maxLength;
                        $newMaxLength = $newKey + 1 - $maxLength;
                    }
                }
            }

            if ($maxLength == 0) {
                $diff = [['-' => $old, '+' => $new]];
            } else {
                $diff = array_merge(
                    $this->diff(array_slice($old, 0, $oldMaxLength), array_slice($new, 0, $newMaxLength)),
                    array_slice($new, $newMaxLength, $maxLength),
                    $this->diff(array_slice($old, $oldMaxLength + $maxLength), array_slice($new, $newMaxLength + $maxLength))
                );
            }
        }

        return $diff;
    }

    protected static function split(?string $value): array
    {
        return explode(PHP_EOL, $value ?? '');
    }
}
