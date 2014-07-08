<?php

namespace mageekguy\atoum;

class report implements observer
{
	protected $locale = null;
	protected $adapter = null;
	protected $title = null;
	protected $writers = array();
	protected $fields = array();
	protected $lastSetFields = array();

	public function __construct()
	{
		$this
			->setLocale()
			->setAdapter()
		;
	}

	public function __toString()
	{
		$string = '';

		foreach ($this->lastSetFields as $field)
		{
			$string .= $field;
		}

		return $string;
	}

	public function setLocale(locale $locale = null)
	{
		$this->locale = $locale ?: new locale();

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}

	public function setAdapter(adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new adapter();

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setTitle($title)
	{
		$this->title = (string) $title;

		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function addField(report\field $field)
	{
		$this->fields[] = $field->setLocale($this->locale);

		return $this;
	}

	public function resetFields()
	{
		$this->fields = array();

		return $this;
	}

	public function getFields()
	{
		return $this->fields;
	}

	public function getWriters()
	{
		return $this->writers;
	}

	public function handleEvent($event, observable $observable)
	{
		$this->lastSetFields = array();

		foreach ($this->fields as $field)
		{
			if ($field->handleEvent($event, $observable) === true)
			{
				$this->lastSetFields[] = $field;
			}
		}

		return $this;
	}

	public function isOverridableBy(report $report)
	{
		return $report !== $this;
	}

	protected function doAddWriter($writer)
	{
		$this->writers[] = $writer;

		return $this;
	}
}
