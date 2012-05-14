<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\tools\diffs
;

/**
 * @property    mageekguyatoum\asserter                       if
 * @property    mageekguyatoum\asserter                       and
 * @property    mageekguyatoum\asserter                       then
 *
 * @method      mageekguyatoum\asserter                       if()
 * @method      mageekguyatoum\asserter                       and()
 * @method      mageekguyatoum\asserter                       then()
 *
 * @method      mageekguyatoum\asserters\adapter              adapter()
 * @method      mageekguyatoum\asserters\afterDestructionOf   afterDestructionOf()
 * @method      mageekguyatoum\asserters\phpArray             array()
 * @method      mageekguyatoum\asserters\boolean              boolean()
 * @method      mageekguyatoum\asserters\castToString         castToString()
 * @method      mageekguyatoum\asserters\phpClass             class()
 * @method      mageekguyatoum\asserters\dateTime             dateTime()
 * @method      mageekguyatoum\asserters\error                error()
 * @method      mageekguyatoum\asserters\exception            exception()
 * @method      mageekguyatoum\asserters\float                float()
 * @method      mageekguyatoum\asserters\hash                 hash()
 * @method      mageekguyatoum\asserters\integer              integer()
 * @method      mageekguyatoum\asserters\mock                 mock()
 * @method      mageekguyatoum\asserters\mysqlDateTime        mysqlDateTime()
 * @method      mageekguyatoum\asserters\object               object()
 * @method      mageekguyatoum\asserters\output               output()
 * @method      mageekguyatoum\asserters\phpArray             phpArray()
 * @method      mageekguyatoum\asserters\phpClass             phpClass()
 * @method      mageekguyatoum\asserters\sizeOf               sizeOf()
 * @method      mageekguyatoum\asserters\stream               stream()
 * @method      mageekguyatoum\asserters\string               string()
 * @method      mageekguyatoum\asserters\testedClass          testedClass()
 * @method      mageekguyatoum\asserters\variable             variable()
 */
class exception extends asserters\object
{
	public function setWith($value, $label = null, $check = true)
	{
		$exception = $value;

		if ($exception instanceof \closure)
		{
			$exception = null;

			try
			{
				$value();
			}
			catch (\exception $exception) {}
		}

		parent::setWith($exception, $label, false);

		if ($check === true)
		{
			if (self::isException($exception) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not an exception'), $this));
			}
			else
			{
				$this->pass();
			}
		}

		return $this;
	}

	public function isInstanceOf($value, $failMessage = null)
	{
		try
		{
			self::check($value, __METHOD__);
		}
		catch (\logicException $exception)
		{
			if (self::classExists($value) === false || (strtolower(ltrim($value, '\\')) !== 'exception' && is_subclass_of($value, 'exception') === false))
			{
				throw new exceptions\logic\invalidArgument('Argument of ' . __METHOD__ . '() must be a \exception instance or an exception class name');
			}
		}

		return parent::isInstanceOf($value, $failMessage);
	}

	public function hasDefaultCode($failMessage = null)
	{
		if ($this->valueIsSet()->value->getCode() === 0)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('code is %s instead of 0'), $this->value->getCode()));
		}
	}

	public function hasCode($code, $failMessage = null)
	{
		if ($this->valueIsSet()->value->getCode() === $code)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('code is %s instead of %s'), $this->value->getCode(), $code));
		}
	}

	public function hasMessage($message, $failMessage = null)
	{
		if ($this->valueIsSet()->value->getMessage() == (string) $message)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('message \'%s\' is not identical to \'%s\''), $this->value->getMessage(), $message));
		}
	}

	public function hasNestedException(\exception $exception = null, $failMessage = null)
	{
		if ($exception === null)
		{
			if ($this->valueIsSet()->value->getPrevious() !== null)
			{
				return $this->pass();
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : $this->getLocale()->_('exception does not contain any nested exception'));
			}
		}
		else
		{
			if ($this->valueIsSet()->value->getPrevious() == $exception)
			{
				return $this->pass();
			}
			else
			{
				$this->fail($failMessage !== null ? $failMessage : $this->getLocale()->_('exception does not contain this nested exception'));
			}
		}
	}

	protected function valueIsSet($message = 'Exception is undefined')
	{
		return parent::valueIsSet($message);
	}

	protected static function check($value, $method)
	{
		if (self::isException($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be an exception instance');
		}
	}

	protected static function isException($value)
	{
		return (parent::isObject($value) === true && $value instanceof \exception === true);
	}
}

?>
