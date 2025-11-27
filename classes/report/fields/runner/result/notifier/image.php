<?php

namespace atoum\atoum\report\fields\runner\result\notifier;

use atoum\atoum\exceptions;
use atoum\atoum\report\fields\runner\result\notifier;

abstract class image extends notifier
{
    protected mixed $directory = null;
    protected mixed $successImage = null;
    protected mixed $failureImage = null;

    public function __toString(): string
    {
        try {
            return parent::__toString();
        } catch (exceptions\runtime $exception) {
            return $exception->getMessage() . PHP_EOL;
        }
    }

    public function setSuccessImage(string $path): static
    {
        $this->successImage = $path;

        return $this;
    }

    public function getSuccessImage(): mixed
    {
        return $this->successImage;
    }

    public function setFailureImage(string $path): static
    {
        $this->failureImage = $path;

        return $this;
    }

    public function getFailureImage(): mixed
    {
        return $this->failureImage;
    }

    public function getImage(bool $success): string
    {
        $image = $success ? $this->getSuccessImage() : $this->getFailureImage();

        if ($this->getAdapter()->file_exists($image) === false) {
            throw new exceptions\runtime(sprintf('File %s does not exist', $image));
        }

        return $image;
    }

    public function send(string $title, string $message, mixed $success): string
    {
        return parent::send($title, $message, $this->getImage($success));
    }
}
