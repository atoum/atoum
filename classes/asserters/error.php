<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\exceptions
;

/**
 * @property    \mageekguy\atoum\asserter                       if
 * @property    \mageekguy\atoum\asserter                       and
 * @property    \mageekguy\atoum\asserter                       then
 *
 * @method      \mageekguy\atoum\asserter                       if()
 * @method      \mageekguy\atoum\asserter                       and()
 * @method      \mageekguy\atoum\asserter                       then()
 *
 * @method      \mageekguy\atoum\asserters\adapter              adapter()
 * @method      \mageekguy\atoum\asserters\afterDestructionOf   afterDestructionOf()
 * @method      \mageekguy\atoum\asserters\phpArray             array()
 * @method      \mageekguy\atoum\asserters\boolean              boolean()
 * @method      \mageekguy\atoum\asserters\castToString         castToString()
 * @method      \mageekguy\atoum\asserters\phpClass             class()
 * @method      \mageekguy\atoum\asserters\dateTime             dateTime()
 * @method      \mageekguy\atoum\asserters\error                error()
 * @method      \mageekguy\atoum\asserters\exception            exception()
 * @method      \mageekguy\atoum\asserters\float                float()
 * @method      \mageekguy\atoum\asserters\hash                 hash()
 * @method      \mageekguy\atoum\asserters\integer              integer()
 * @method      \mageekguy\atoum\asserters\mock                 mock()
 * @method      \mageekguy\atoum\asserters\mysqlDateTime        mysqlDateTime()
 * @method      \mageekguy\atoum\asserters\object               object()
 * @method      \mageekguy\atoum\asserters\output               output()
 * @method      \mageekguy\atoum\asserters\phpArray             phpArray()
 * @method      \mageekguy\atoum\asserters\phpClass             phpClass()
 * @method      \mageekguy\atoum\asserters\sizeOf               sizeOf()
 * @method      \mageekguy\atoum\asserters\stream               stream()
 * @method      \mageekguy\atoum\asserters\string               string()
 * @method      \mageekguy\atoum\asserters\testedClass          testedClass()
 * @method      \mageekguy\atoum\asserters\variable             variable()
 */
class error extends \mageekguy\atoum\asserter
{
	protected $message = null;
	protected $type = null;
	protected $messageIsPattern = false;

	public function setWith($message = null, $type = null)
	{
		return $this
			->withType($type)
			->withMessage($message)
		;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function getType()
	{
		return $this->type;
	}

	public function exists()
	{
		$score = $this->getScore();

		$key = $score->errorExists($this->message, $this->type, $this->messageIsPattern);

		if ($key !== null)
		{
			$score->deleteError($key);
			$this->pass();
		}
		else
		{
			$failReason = '';

			switch (true)
			{
				case $this->type === null && $this->message === null:
					$failReason = $this->getLocale()->_('error does not exist');
					break;

				case $this->type === null && $this->message !== null:
					$failReason = sprintf($this->getLocale()->_('error with message \'%s\' does not exist'), $this->message);
					break;

				case $this->type !== null && $this->message === null:
					$failReason = sprintf($this->getLocale()->_('error of type %s does not exist'), self::getAsString($this->type));
					break;

				default:
					$failReason = sprintf($this->getLocale()->_('error of type %s with message \'%s\' does not exist'), self::getAsString($this->type), $this->message);
			}

			$this->fail($failReason);
		}

		return $this;
	}

	public function notExists()
	{
		$score = $this->getScore();

		$key = $score->errorExists($this->message, $this->type, $this->messageIsPattern);

		if ($key === null)
		{
			$this->pass();
		}
		else
		{
			$failReason = '';

			switch (true)
			{
				case $this->type === null && $this->message === null:
					$failReason = $this->getLocale()->_('error exists');
					break;

				case $this->type === null && $this->message !== null:
					$failReason = sprintf($this->getLocale()->_('error with message \'%s\' exists'), $this->message);
					break;

				case $this->type !== null && $this->message === null:
					$failReason = sprintf($this->getLocale()->_('error of type %s exists'), self::getAsString($this->type));
					break;

				default:
					$failReason = sprintf($this->getLocale()->_('error of type %s with message \'%s\' exists'), self::getAsString($this->type), $this->message);
			}

			$this->fail($failReason);
		}

		return $this;
	}

	public function withType($type)
	{
		$this->type = $type;

		return $this;
	}

	public function withAnyType()
	{
		$this->type = null;

		return $this;
	}

	public function messageIsPattern()
	{
		return $this->messageIsPattern;
	}

	public function withMessage($message)
	{
		$this->message = $message;
		$this->messageIsPattern = false;

		return $this;
	}

	public function withPattern($pattern)
	{
		$this->message = $pattern;
		$this->messageIsPattern = true;

		return $this;
	}

	public function withAnyMessage()
	{
		$this->message = null;
		$this->messageIsPattern = false;

		return $this;
	}

	public static function getAsString($errorType)
	{
		switch ($errorType)
		{
			case E_ERROR:
				return 'E_ERROR';

			case E_WARNING:
				return 'E_WARNING';

			case E_PARSE:
				return 'E_PARSE';

			case E_NOTICE:
				return 'E_NOTICE';

			case E_CORE_ERROR:
				return 'E_CORE_ERROR';

			case E_CORE_WARNING:
				return 'E_CORE_WARNING';

			case E_COMPILE_ERROR:
				return 'E_COMPILE_ERROR';

			case E_COMPILE_WARNING:
				return 'E_COMPILE_WARNING';

			case E_USER_ERROR:
				return 'E_USER_ERROR';

			case E_USER_WARNING:
				return 'E_USER_WARNING';

			case E_USER_NOTICE:
				return 'E_USER_NOTICE';

			case E_STRICT:
				return 'E_STRICT';

			case E_RECOVERABLE_ERROR:
				return 'E_RECOVERABLE_ERROR';

			case E_DEPRECATED:
				return 'E_DEPRECATED';

			case E_USER_DEPRECATED:
				return 'E_USER_DEPRECATED';

			case E_ALL:
				return 'E_ALL';

			default:
				return 'UNKNOWN';
		}
	}
}

?>
