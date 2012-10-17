<?php

namespace mageekguy\atoum;

class report implements observer
{
	protected $title = null;
	protected $factory = null;
	protected $locale = null;
	protected $adapter = null;
	protected $writers = array();
	protected $fields = array();
	protected $lastSetFields = array();

	public function __construct()
	{
		$this->setLocale();
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
		$this->fields[] = $field;

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

	public function __toString()
	{
		$string = '';

		foreach ($this->lastSetFields as $field)
		{
			$string .= $field;
		}

		return $string;
	}

	protected function doAddWriter($writer)
	{
		$this->writers[] = $writer;

		return $this;
	}
}
