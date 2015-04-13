<?php

namespace mageekguy\atoum\scripts\treemap;

use
	mageekguy\atoum\exceptions
;

class categorizer
{
	protected $name = '';
	protected $callback = null;
	protected $minDepthColor = '#94ff5a';
	protected $maxDepthColor = '#00500f';

	public function __construct($name)
	{
		$this->name = $name;

		$this->setCallback();
	}

	public function getName()
	{
		return $this->name;
	}

	public function setCallback(\closure $callback = null)
	{
		$this->callback = $callback ?: function() { return false; };

		return $this;
	}

	public function getCallback()
	{
		return $this->callback;
	}

	public function setMinDepthColor($color)
	{
		$this->minDepthColor = static::checkColor($color);

		return $this;
	}

	public function getMinDepthColor()
	{
		return $this->minDepthColor;
	}

	public function setMaxDepthColor($color)
	{
		$this->maxDepthColor = static::checkColor($color);

		return $this;
	}

	public function getMaxDepthColor()
	{
		return $this->maxDepthColor;
	}

	public function categorize(\splFileInfo $file)
	{
		return call_user_func_array($this->callback, array($file));
	}

	protected static function checkColor($color)
	{
		if (preg_match('/^#?[a-f0-9]{6}$/i', $color) === 0)
		{
			throw new exceptions\logic\invalidArgument('Color must be in hexadecimal format');
		}

		return '#' . ltrim($color, '#');
	}
}
