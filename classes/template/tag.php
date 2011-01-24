<?php

namespace mageekguy\atoum\template;

class tag extends block
{
	private $tag = '';
	private $id = null;
	private $html = false;
	private $line = null;
	private $offset = null;

	public function __construct($tag, $data = null, $line = null, $offset = null)
	{
		if (self::checkTag($tag, $line, $offset) === true && self::checkLine($line) === true && self::checkOffset($offset) === true)
		{
			$this->tag = $tag;
			$this->line = $line;
			$this->offset = $offset;
			parent::__construct($data);
		}
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

	public function getData()
	{
		return ($this->htmlIsEnabled() === false ? parent::getData() : htmlentities(parent::getData()));
	}

	public function htmlIsEnabled()
	{
		return ($this->html === true);
	}

	public function htmlIsDisabled()
	{
		return ($this->htmlIsEnabled() === false);
	}

	public function enableHtml()
	{
		$this->html = true;
		return $this;
	}

	public function disableHtml()
	{
		$this->html = false;
		return $this;
	}

	public function setId($id, $check = true)
	{
		if ($this->getById($id) !== null)
		{
			trigger_error('Id \'' . $id . '\' is already defined', E_USER_ERROR);
		}
		else
		{
			$this->id = $id;
			return $this;
		}
	}

	public function unsetId()
	{
		$this->id = null;
		return $this;
	}

	private static function checkLine($line)
	{
		if ($line === null || self::isGreaterThanZero($line) === true)
		{
			return true;
		}
		else
		{
			trigger_error('Line must be greater than 0', E_USER_ERROR);
			return false;
		}
	}

	private static function checkOffset($offset)
	{
		if ($offset === null || self::isGreaterThanZero($offset) === true)
		{
			return true;
		}
		else
		{
			trigger_error('Offset must be greater than 0', E_USER_ERROR);
			return false;
		}
	}

	private static function checkTag($tag, $line = null, $offset = null)
	{
		if (is_string($tag) === true && $tag != '')
		{
			return true;
		}
		else
		{
			$error = $tag != '' ? 'Tag must be a string' : 'Tag must not be empty';

			if ($line !== null)
			{
				$error .= ' at line \'' . $line . '\'';
			}

			if ($offset !== null)
			{
				$error .= ' at offset \'' . $offset . '\'';
			}

			trigger_error($error, E_USER_ERROR);

			return false;
		}
	}

	private static function isGreaterThanZero($value)
	{
		return (is_numeric($value) === true && $value > 0);
	}
}

?>
