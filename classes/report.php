<?php

namespace atoum\atoum;

class report implements observer
{
    protected ?locale $locale = null;
    protected ?adapter $adapter = null;
    protected ?string $title = null;
    protected array $writers = [];
    protected array $fields = [];
    protected array $lastSetFields = [];

    public function __construct()
    {
        $this
            ->setLocale()
            ->setAdapter()
        ;
    }

    public function __toString(): string
    {
        $string = '';

        foreach ($this->lastSetFields as $field) {
            $string .= $field;
        }

        return $string;
    }

    public function setLocale(?locale $locale = null): static
    {
        $this->locale = $locale ?: new locale();

        return $this;
    }

    public function getLocale(): locale
    {
        return $this->locale;
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

    public function setTitle(string $title): static
    {
        $this->title = (string) $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function addField(report\field $field): static
    {
        $this->fields[] = $field->setLocale($this->locale);

        return $this;
    }

    public function resetFields(): static
    {
        $this->fields = [];

        return $this;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getWriters(): array
    {
        return $this->writers;
    }

    public function handleEvent(string $event, observable $observable)
    {
        $this->lastSetFields = [];

        foreach ($this->fields as $field) {
            if ($field->handleEvent($event, $observable) === true) {
                $this->lastSetFields[] = $field;
            }
        }

        return $this;
    }

    public function isOverridableBy(self $report): bool
    {
        return $report !== $this;
    }

    protected function doAddWriter(mixed $writer): static
    {
        $this->writers[] = $writer;

        return $this;
    }
}
