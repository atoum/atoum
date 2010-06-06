<?php

namespace mageekguy\tests\unit\asserters;

use \mageekguy\tests\unit\reporter;

class error extends \mageekguy\tests\unit\asserter
{
	protected $message = null;
	protected $type = null;

	public function setWith($message = null, $type = null)
	{
		$this->message = $message;
		$this->type = $type;

		return $this;
	}

	public function exists()
	{
		$key = null;

		$key = $this->score->errorExists($this->message, $this->type);

		if ($key !== null)
		{
			$this->score->deleteError($key);
			$this->pass();
		}
		else
		{
			$failReason = '';

			switch (true)
			{
				case $this->type === null && $this->message === null:
					$failReason = $this->locale->_('error does not exist');
					break;

				case $this->type === null && $this->message !== nul:
					$failReason = sprintf($this->locale->_('error with message \'%s\' does not exist'), $this->message);
					break;

				case $this->type !== null && $this->message === null:
					$failReason = sprintf($this->locale->_('error of type %s does not exist'), reporter::getErrorLabel($this->type));
					break;

				default:
					$failReason = sprintf($this->locale->_('error of type %s with message \'%s\' does not exist'), reporter::getErrorLabel($this->type), $this->message);
			}

			$this->fail($failReason);
		}

		return $this;
	}

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new \logicException('Argument must be set at index 0');
		}

		if (array_key_exists(1, $arguments) === false)
		{
			throw new \logicException('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0], $arguments[1]);
	}
}

?>
