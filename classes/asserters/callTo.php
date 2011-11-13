<?php

namespace mageekguy\atoum\asserters;
// ©◊this->assert->callTo(callbackQuiVaBien)->withArguments('a', 'b', 'c')->return('abc')->hasOutput('abc')
use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class callTo extends asserters\variable
{
	protected $arguments = array();
	protected $return = null;
	protected $output = null;
	protected $isExecuted = false;

	public function setWith($value, $label = null)
	{
		parent::setWith($value, $label);

		if (self::isCallback($this->value) === true)
		{
			$this->pass();
		}
		else
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a callback'), $this));
		}

		return $this;
	}

	public function __call($method, $arguments)
	{
		if($method == 'return')
		{
			return call_user_func_array(array($this, 'checkReturn'), $arguments);
		}
		elseif (stripos($method, 'returnHas') === 0) {
			$generator = strtolower($method{9}) . substr($method, 10);
			return $this->generator->$generator($this->return);
		}
		return parent::__call($method, $arguments);
	}

	public function withArguments()
	{
		$this->arguments = func_get_args();
		$this->isExecuted = false;
		return $this;
	}

	private function checkReturn($compareTo, $failMessage = null)
	{
		$this->executeCallback();
		if ($this->return === $compareTo)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('Values don\'t match')));
		}

		return $this;
	}

	public function hasOutput($compareTo, $failMessage = null)
	{
		$this->executeCallback();
		if ($this->output === $compareTo)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('Values don\'t match')));
		}

		return $this;
	}

	public function outputHasString()
	{
		$this->executeCallback();
		return $this->generator->string($this->output);
	}

	private function executeCallback()
	{
		if($this->isExecuted === false) {
			$this->isExecuted = true;
			ob_start();
			$this->return = call_user_func_array($this->value, $this->arguments);
			$this->output = ob_get_contents();
			ob_end_clean();
		}
	}

	protected static function check($value, $method)
	{
		if (self::isCallback($value) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument of ' . $method . '() must be a valid callback');
		}
	}

	protected static function isCallback($value)
	{
		return (is_callable($value) === true);
	}
}

?>