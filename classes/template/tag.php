<?php

namespace atoum\atoum\template;

use atoum\atoum;
use atoum\atoum\exceptions;

class tag extends atoum\template
{
    private string $tag = '';
    private ?string $id = null;
    private ?int $line = null;
    private ?int $offset = null;

    public function __construct(string $tag, mixed $data = null, ?int $line = null, ?int $offset = null)
    {
        $tag = (string) $tag;

        if ($tag === '') {
            throw new exceptions\logic('Tag must not be an empty string');
        }

        if ($line !== null) {
            $line = (int) $line;

            if ($line <= 0) {
                throw new exceptions\logic('Line must be greater than 0');
            }
        }

        if ($offset !== null) {
            $offset = (int) $offset;

            if ($offset <= 0) {
                throw new exceptions\logic('Offset must be greater than 0');
            }
        }

        parent::__construct($data);

        $this->tag = $tag;
        $this->line = $line;
        $this->offset = $offset;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getLine(): ?int
    {
        return $this->line;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function setId($id)
    {
        $id = (string) $id;

        if ($id === '') {
            throw new exceptions\logic('Id must not be empty');
        }

        if (($tagWithSameId = $this->getById($id)) !== null) {
            $line = $tagWithSameId->getLine();
            $offset = $tagWithSameId->getOffset();

            throw new exceptions\logic('Id \'' . $id . '\' is already defined in line ' . ($line !== null ?: 'unknown') . ' at offset ' . ($offset !== null ?: 'unknown'));
        }

        $this->id = $id;

        return $this;
    }

    public function unsetId()
    {
        $this->id = null;

        return $this;
    }

    public function setAttribute(string $name, mixed $value): static
    {
        switch (true) {
            case $name == 'id':
                $this->setId($value);
                break;

            default:
                throw new exceptions\logic('Attribute \'' . $name . '\' is unknown');
        }

        return $this;
    }

    public function unsetAttribute(string $name): static
    {
        switch ($name) {
            case 'id':
                $this->unsetId();
                break;

            default:
                throw new exceptions\logic('Attribute \'' . $name . '\' is unknown');
        }

        return $this;
    }
}
