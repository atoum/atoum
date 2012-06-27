<?php

namespace mageekguy\atoum\template;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class tag extends atoum\template
{
	private $tag = '';
	private $id = null;
	private $line = null;
	private $offset = null;

	public function __construct($tag, $data = null, $line = null, $offset = null)
	{
		$tag = (string) $tag;

		if ($tag === '')
		{
			throw new exceptions\logic('Tag must not be an empty string');
		}

		if ($line !== null)
		{
			$line = (int) $line;

			if ($line <= 0)
			{
				throw new exceptions\logic('Line must be greater than 0');
			}
		}

		if ($offset !== null)
		{
			$offset = (int) $offset;

			if ($offset <= 0)
			{
				throw new exceptions\logic('Offset must be greater than 0');
			}
		}

		parent::__construct($data);

		$this->tag = $tag;
		$this->line = $line;
		$this->offset = $offset;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getTag()
	{
		return $this->tag;
	}

	public function getLine()
	{
		return $this->line;
	}

	public function getOffset()
	{
		return $this->offset;
	}

	public function setId($id)
	{
		$id = (string) $id;

		if ($id === '')
		{
			throw new exceptions\logic('Id must not be empty');
		}

		if (($tagWithSameId = $this->getById($id)) !== null)
		{
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

	public function setAttribute($name, $value)
	{
		switch (true)
		{
			case $name == 'id':
				$this->setId($value);
				break;

			default:
				throw new exceptions\logic('Attribute \'' . $name . '\' is unknown');
		}

		return $this;
	}

	public function unsetAttribute($name)
	{
		switch ($name)
		{
			case 'id':
				$this->unsetId();
				break;

			default:
				throw new exceptions\logic('Attribute \'' . $name . '\' is unknown');
		}

		return $this;
	}
}
