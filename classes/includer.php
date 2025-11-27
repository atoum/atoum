<?php

namespace atoum\atoum;

class includer
{
    protected ?adapter $adapter = null;
    protected array $errors = [];

    private string $path = '';

    public function __construct(?adapter $adapter = null)
    {
        $this->setAdapter($adapter);
    }

    public function resetErrors(): static
    {
        $this->errors = [];

        return $this;
    }

    public function setAdapter(?adapter $adapter = null): static
    {
        $this->adapter = $adapter ?: new adapter();

        return $this;
    }

    public function getAdapter(): adapter
    {
        return $this->adapter;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function includePath(string $path, ?\Closure $closure = null): static
    {
        $this->resetErrors();

        $this->path = (string) $path;

        $errorHandler = $this->adapter->set_error_handler([$this, 'errorHandler']);

        $closure = $closure ?: function ($path) {
            include_once($path);
        };

        $closure($this->path);

        $this->adapter->restore_error_handler();

        if (count($this->errors) > 0) {
            $realpath = (parse_url($this->path, PHP_URL_SCHEME) !== null ? $this->path : (realpath($this->path) ?: $this->path));

            if (in_array($realpath, $this->adapter->get_included_files(), true) === false) {
                throw new includer\exception('Unable to include \'' . $this->path . '\'');
            }

            if ($errorHandler !== null) {
                foreach ($this->errors as $error) {
                    call_user_func_array($errorHandler, $error);
                }

                $this->errors = [];
            }
        }

        return $this;
    }

    public function getFirstError(): ?array
    {
        $firstError = null;

        if (count($this->errors) > 0) {
            $firstError = $this->errors[0];
        }

        return $firstError;
    }

    public function errorHandler(int $error, string $message, string $file, int $line, mixed $context = []): bool
    {
        $errorReporting = $this->adapter->error_reporting();

        if ($errorReporting & $error) {
            foreach (array_reverse(debug_backtrace()) as $trace) {
                if (isset($trace['file']) === true && $trace['file'] === $this->path) {
                    $file = $this->path;
                    $line = $trace['line'];

                    break;
                }
            }

            $this->errors[] = [$error, $message, $file, $line, $context];
        }

        return true;
    }
}
