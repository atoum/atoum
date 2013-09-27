<?php

namespace mageekguy\atoum\asserters\call;

use
	mageekguy\atoum,
	mageekguy\atoum\php,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class arguments extends asserter implements \arrayAccess
{
	protected $callee;
	protected $callAsserter;
	protected $arguments = array();
	protected $currentArgument;

	public function __construct(asserters\call $callAsserter, asserter\generator $generator = null)
	{
		$this->callAsserter = $callAsserter;

		parent::__construct($generator ?: $callAsserter->getGenerator());
	}

	public function __call($method, $arguments)
	{
		return call_user_func_array(array($this->callAsserter, $method), $arguments);
	}

	public function __get($asserter)
	{
		return $this->callAsserter->$asserter;
	}

	public function setWith($adapter)
	{
		$this->callee = $adapter;

		if ($this->callee instanceof atoum\test\adapter === false)
		{
			$this->fail(sprintf($this->generator->getLocale()->_('%s is not a test adapter'), $this->getTypeOf($this->callee)));
		}

		return $this;
	}

	public function getCallee()
	{
		return $this->callee;
	}

	public function getCallAsserter()
	{
		return $this->callAsserter;
	}

	public function getArgumentAsserter($argument)
	{
		return $this->arguments[$this->checkArgument($argument)];
	}

	public function offsetSet($argument, $value)
	{
		return $this->atArgument($argument)->isEqualTo($value);
	}

	public function offsetGet($call)
	{
		return $this->atArgument($call);
	}

	public function offsetUnset($argument)
	{
		unset($this->arguments[$argument]);

		return $this;
	}

	public function offsetExists($argument)
	{
		return array_key_exists($argument, $this->arguments);
	}

	public function atArgument($argument)
	{
		$this->currentArgument = self::checkArgument($argument);

		return $this;
	}

	protected static function checkArgument($argument)
	{
		$call = (int) $argument;

		if ($call < 0)
		{
			throw new exceptions\logic\invalidArgument('Argument number must be greater than or equal to zero');
		}

		return $call;
	}

	public function isIdenticalTo($expected)
	{
		$this->arguments[$this->currentArgument] = static::curry('variable', __FUNCTION__, $expected, $this->generator);

		return $this;
	}

	public function isEqualTo($expected)
	{
		$this->arguments[$this->currentArgument] = static::curry('variable', __FUNCTION__, $expected, $this->generator);

		return $this;
	}

	public function isNull()
	{
		return $this->isIdenticalTo(null);
	}

	public function isInstanceOf($expected)
	{
		if (static::isObject($expected) === false && class_exists($expected) === false && interface_exists($expected) === false)
		{
			throw new exceptions\logic('Argument of ' . __METHOD__ . '() must be a class instance or a class name');
		}

		$this->arguments[$this->currentArgument] = static::curry('object', __FUNCTION__, $expected, $this->generator);

		return $this;
	}

	protected static function curry($asserter, $assertion, $expected, asserter\generator $generator = null)
	{
		$generator = $generator ?: new asserter\generator();

		return function($actual) use ($asserter, $assertion, $expected, $generator) {
			try {
				$generator->getAsserterInstance($asserter)->setGenerator()->setWith($actual)->$assertion($expected);

				return true;
			}
			catch(atoum\asserter\exception $e)
			{
				return false;
			}
		};
	}


	public function isCallable()
	{
		$generator = $this->generator;
		$this->arguments[$this->currentArgument] = function($actual) use ($generator) {
			try {
				$generator->getAsserterInstance('variable')->setGenerator()->setWith($actual)->isCallable();

				return true;
			}
			catch(atoum\asserter\exception $e)
			{
				return false;
			}
		};

		return $this;
	}

	public function isArray()
	{
		$generator = $this->generator;
		$this->arguments[$this->currentArgument] = function($actual) use ($generator) {
			try {
				$generator->array($actual)->setGenerator();

				return true;
			}
			catch(atoum\asserter\exception $e)
			{
				return false;
			}
		};

		return $this;
	}

	public function once($failMessage = null)
	{
		return $this->exactly(1, $failMessage);
	}

	public function twice($failMessage = null)
	{
		return $this->exactly(1, $failMessage);
	}

	public function thrice($failMessage = null)
	{
		return $this->exactly(1, $failMessage);
	}

	public function never($failMessage = null)
	{
		return $this->exactly(0, $failMessage);
	}

	public function exactly($number, $failMessage = null)
	{
		$call = $this->getCallAsserter()->getCall();
		$calls = $this->callee->getCalls($call->getFunction());

		foreach ($calls as $c => $arguments)
		{
			foreach ($arguments as $argument => $value) {
				if (array_key_exists($argument, $this->arguments) &&  $this->arguments[$argument]($value) === false)
				{
					unset($calls[$c]);
					break;
				}
			}
		}

		if (($callsNumber = sizeof($calls)) === $number)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf(
					$this->generator->getLocale()->__(
						'function %s is called %d time instead of %d',
						'function %s is called %d times instead of %d',
						$callsNumber
					),
					$call,
					$callsNumber,
					$number
				) . $this->getCallsAsString($call)
			);
		}


		return $this;
	}

	protected function getCallsAsString($call)
	{
		$string = '';

		if (($calls  = $this->callee->getCalls($call->getFunction())) !== null)
		{
			$format = '[%' . strlen((string) sizeof($calls)) . 's] %s';

			$phpCalls = array();

			foreach (array_values($calls) as $c => $arguments)
			{
				$phpCalls[] = sprintf($format, $c + 1, new php\call($call->getFunction(), $arguments));
			}

			$string = PHP_EOL . join(PHP_EOL, $phpCalls);
		}

		return $string;
	}

	protected static function isObject($value)
	{
		return (is_object($value) === true);
	}
} 