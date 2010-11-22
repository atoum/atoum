<?php

namespace mageekguy\atoum\asserters;

use \mageekguy\atoum\exceptions;

class collection extends \mageekguy\atoum\asserters\variable
{
	public function setWith($variable)
	{
		parent::setWith($variable);

		if (self::isArray($this->variable) === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not an array'), $this));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function isEmpty($failMessage = null)
	{
		if (sizeof($this->variableIsSet()->variable) == 0)
		{
			return $this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(
				($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not empty'), $this)) .
				PHP_EOL .
				$diff->setReference(array())->setData($this->variable)
			);
		}
	}

	public function isNotEmpty()
	{
		if (sizeof($this->variableIsSet()->variable) > 0)
		{
			return $this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(
				($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is empty'), $this)) .
				PHP_EOL .
				$diff->setReference($this->variable)->setData(array())
			);
		}
	}

	public function contain($variable, $failMessage = null)
	{
		if (in_array($variable, $this->variableIsSet()->variable) === true)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s does not contain %s'), $this, $this->toString($variable)));
		}
	}

	protected static function check($variable, $method)
	{
		if (self::isArray($variable) === false)
		{
			throw new exceptions\logic\argument('Argument of ' . $method . '() must be an array');
		}
	}

	protected static function isArray($variable)
	{
		return (is_array($variable) === true);
	}
}

?>
