<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\asserter
;

class error extends asserter
{
	protected $score = null;
	protected $message = null;
	protected $type = null;
	protected $messageIsPattern = false;

	public function __construct(asserter\generator $generator = null, test\score $score = null, atoum\locale $locale = null)
	{
		parent::__construct($generator, null, $locale);

		$this->setScore($score);
	}

	public function __get($asserter)
	{
		switch (strtolower($asserter))
		{
			case 'exists':
			case 'notexists':
			case 'withanytype':
			case 'withanymessage':
				return $this->{$asserter}();

			default:
				return parent::__get($asserter);
		}
	}

	public function setWithTest(test $test)
	{
		$this->setScore($test->getScore());

		return parent::setWithTest($test);
	}

	public function setWith($message = null, $type = null)
	{
		return $this
			->withType($type)
			->withMessage($message)
		;
	}

	public function setScore(test\score $score = null)
	{
		$this->score = $score ?: new test\score();

		return $this;
	}

	public function getScore()
	{
		return $this->score;
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
		$key = $this->score->errorExists($this->message, $this->type, $this->messageIsPattern);

		if ($key !== null)
		{
			$this->score->deleteError($key);
			$this->pass();
		}
		else
		{
			switch (true)
			{
				case $this->type === null && $this->message === null:
					$failReason = $this->_('error does not exist');
					break;

				case $this->type === null && $this->message !== null:
					$failReason = $this->_('error with message \'%s\' does not exist', $this->message);
					break;

				case $this->type !== null && $this->message === null:
					$failReason = $this->_('error of type %s does not exist', self::getAsString($this->type));
					break;

				default:
					$failReason = $this->_('error of type %s with message \'%s\' does not exist', self::getAsString($this->type), $this->message);
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
			switch (true)
			{
				case $this->type === null && $this->message === null:
					$failReason = $this->_('error exists');
					break;

				case $this->type === null && $this->message !== null:
					$failReason = $this->_('error with message \'%s\' exists', $this->message);
					break;

				case $this->type !== null && $this->message === null:
					$failReason = $this->_('error of type %s exists', self::getAsString($this->type));
					break;

				default:
					$failReason = $this->_('error of type %s with message \'%s\' exists', self::getAsString($this->type), $this->message);
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
