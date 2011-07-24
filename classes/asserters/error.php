<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\reporter,
	mageekguy\atoum\exceptions
;

class error extends \mageekguy\atoum\asserter
{
	protected $message = null;
	protected $type = null;

	public function setWith($message = null, $type = null)
	{
		$this->message = $message;
		$this->type = $type;

		return $this;
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
		$key = null;

		$score = $this->getScore();

		$key = $score->errorExists($this->message, $this->type);

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
					$failReason = sprintf($this->getLocale()->_('error of type %s does not exist'), reporter::getErrorLabel($this->type));
					break;

				default:
					$failReason = sprintf($this->getLocale()->_('error of type %s with message \'%s\' does not exist'), reporter::getErrorLabel($this->type), $this->message);
			}

			$this->fail($failReason);
		}

		return $this;
	}
}

?>
