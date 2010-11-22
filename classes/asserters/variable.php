<?php

namespace mageekguy\atoum\asserters;

use \mageekguy\atoum;
use \mageekguy\atoum\exceptions;
use \mageekguy\atoum\tools\diffs;

class variable extends atoum\asserter
{
	protected $isSet = false;
	protected $isSetByReference = false;
	protected $variable = null;

	public function __toString()
	{
		return $this->toString($this->variable);
	}

	public function wasSet()
	{
		return ($this->isSet === true);
	}

	public function setWith($variable)
	{
		$this->variable = $variable;
		$this->isSet = true;
		$this->isSetByReference = false;

		return $this;
	}

	public function setByReferenceWith(& $variable)
	{
		$this->variable = & $variable;
		$this->isSet = true;
		$this->isSetByReference = true;

		return $this;
	}

	public function reset()
	{
		$this->variable = null;
		$this->isSet = false;
		$this->isSetByReference = false;

		return $this;
	}

	public function getVariable()
	{
		return $this->variable;
	}

	public function isSetByReference()
	{
		return ($this->isSet === true && $this->isSetByReference === true);
	}

	public function isEqualTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		if ($this->variableIsSet()->variable == $variable)
		{
			return $this->pass();
		}
		else
		{
			$diff = new diffs\variable();

			$this->fail(
				($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not equal to %s'), $this, $this->toString($variable))) .
				PHP_EOL .
				$diff->setReference($variable)->setData($this->variable)
			);
		}
	}

	public function isNotEqualTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		if ($this->variableIsSet()->variable != $variable)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is equal to %s'), $this, $this->toString($variable)));
		}
	}

	public function isIdenticalTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		if ($this->variableIsSet()->variable === $variable)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not identical to %s'), $this, $this->toString($variable)));
		}
	}

	public function isNotIdenticalTo($variable, $failMessage = null)
	{
		self::check($variable, __METHOD__);

		if ($this->variableIsSet()->variable !== $variable)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is identical to %s'), $this, $this->toString($variable)));
		}
	}

	public function isNull($failMessage = null)
	{
		if ($this->variableIsSet()->variable === null)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not null'), $this));
		}
	}

	public function isNotNull($failMessage = null)
	{
		if ($this->variableIsSet()->variable !== null)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is null'), $this));
		}
	}

	public function isReferenceTo(& $reference, $failMessage = null)
	{
		if ($this->variableIsSet()->isSetByReference() === false)
		{
			throw new exceptions\logic('Variable is not set by reference');
		}

		if (is_object($this->variable) === true && is_object($reference) === true)
		{
			if ($this->variable === $reference)
			{
				return $this->pass();
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not a reference to %s'), $this, $this->toString($reference)));
			}
		}
		else
		{
			$tmp = $reference;
			$reference = uniqid(mt_rand());
			$isReference = ($this->variable === $reference);
			$reference = $tmp;

			if ($isReference === true)
			{
				return $this->pass();
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not a reference to %s'), $this, $this->toString($reference)));
			}
		}

		return $this;
	}

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\argument('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0]);
	}

	protected function variableIsSet($message = 'Variable is undefined')
	{
		if ($this->isSet === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}

	protected static function check($variable, $method) {}
}

?>
