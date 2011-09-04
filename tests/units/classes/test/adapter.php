<?php

namespace mageekguy\atoum\tests\units\test;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../runner.php';

class adapter extends atoum\test
{
	public function test__construct()
	{
		$adapter = new atoum\test\adapter();

		$this->assert
			->array($adapter->getCallables())->isEmpty()
			->array($adapter->getCalls())->isEmpty()
		;
	}

	public function test__set()
	{
		$adapter = new atoum\test\adapter();

		$closure = function() {};

		$this->assert
			->when(function() use ($adapter, & $closure) { $adapter->md5 = $closure = function() {}; })
			->object($adapter->md5->getClosure())->isIdenticalTo($closure)
		;

		$adapter->md5 = $return = uniqid();

		$this->assert
			->object($adapter->md5)->isInstanceOf('mageekguy\atoum\test\adapter\callable')
			->object($adapter->MD5)->isInstanceOf('mageekguy\atoum\test\adapter\callable')
			->string($adapter->invoke('md5'))->isEqualTo($return)
			->string($adapter->invoke('MD5'))->isEqualTo($return)
		;

		$adapter->MD5 = $return = uniqid();

		$this->assert
			->object($adapter->md5)->isInstanceOf('mageekguy\atoum\test\adapter\callable')
			->object($adapter->MD5)->isInstanceOf('mageekguy\atoum\test\adapter\callable')
			->string($adapter->invoke('md5'))->isEqualTo($return)
			->string($adapter->invoke('MD5'))->isEqualTo($return)
		;
	}

	public function test__get()
	{
		$adapter = new atoum\test\adapter();

		$adapter->md5 = $closure = function() {};

		$this->assert
			->object($adapter->md5->getClosure())->isIdenticalTo($closure)
			->object($adapter->MD5->getClosure())->isIdenticalTo($closure)
		;

		$adapter->md5 = uniqid();

		$this->assert
			->object($adapter->md5->getClosure())->isInstanceOf('closure')
			->object($adapter->MD5->getClosure())->isInstanceOf('closure')
		;
	}

	public function test__isset()
	{
		$adapter = new atoum\test\adapter();

		$this->assert
			->boolean(isset($adapter->md5))->isFalse()
		;

		$adapter->{$function = strtolower(uniqid())} = function() {};

		$this->assert
			->boolean(isset($adapter->{$function}))->isTrue()
			->boolean(isset($adapter->{strtoupper($function)}))->isTrue()
		;

		$adapter->{$function = strtoupper(uniqid())} = function() {};

		$this->assert
			->boolean(isset($adapter->{$function}))->isTrue()
			->boolean(isset($adapter->{strtolower($function)}))->isTrue()
		;

		$adapter->{$function = strtolower(uniqid())} = uniqid();

		$this->assert
			->boolean(isset($adapter->{$function}))->isTrue()
			->boolean(isset($adapter->{strtoupper($function)}))->isTrue()
		;

		$adapter->{$function = strtoupper(uniqid())} = uniqid();

		$this->assert
			->boolean(isset($adapter->{$function}))->isTrue()
			->boolean(isset($adapter->{strtolower($function)}))->isTrue()
		;
	}

	public function test__unset()
	{
		$this->assert
			->when(function() use (& $adapter) { $adapter = new atoum\test\adapter(); })
				->array($adapter->getCallables())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) { unset($adapter->md5); })
				->array($adapter->getCallables())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) { unset($adapter->MD5); })
				->array($adapter->getCallables())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) { $adapter->md5 = uniqid(); $adapter->md5(uniqid()); })
				->array($adapter->getCallables())->isNotEmpty()
				->array($adapter->getCalls())->isNotEmpty()
			->when(function() use ($adapter) { unset($adapter->{uniqid()}); })
				->array($adapter->getCallables())->isNotEmpty()
				->array($adapter->getCalls())->isNotEmpty()
			->when(function() use ($adapter) { unset($adapter->md5); })
				->array($adapter->getCallables())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) { $adapter->MD5 = uniqid(); $adapter->MD5(uniqid()); })
				->array($adapter->getCallables())->isNotEmpty()
				->array($adapter->getCalls())->isNotEmpty()
			->when(function() use ($adapter) { unset($adapter->{uniqid()}); })
				->array($adapter->getCallables())->isNotEmpty()
				->array($adapter->getCalls())->isNotEmpty()
			->when(function() use ($adapter) { unset($adapter->MD5); })
				->array($adapter->getCallables())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
		;
	}

	public function test__call()
	{
		$adapter = new atoum\test\adapter();

		$this->assert
			->string($adapter->md5($hash = uniqid()))->isEqualTo(md5($hash))
			->string($adapter->MD5($hash = uniqid()))->isEqualTo(md5($hash))
		;

		$adapter->md5 = $md5 = uniqid();

		$this->assert
			->string($adapter->md5($hash))->isEqualTo($md5)
			->string($adapter->MD5($hash))->isEqualTo($md5)
		;

		$adapter->md5 = $md5 = uniqid();

		$this->assert
			->string($adapter->md5($hash))->isEqualTo($md5)
			->string($adapter->MD5($hash))->isEqualTo($md5)
		;

		$this->assert
			->exception(function() use ($adapter) {
						$adapter->require(uniqid());
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Function \'require()\' is not callable by an adapter')
			->exception(function() use ($adapter) {
						$adapter->REQUIRE(uNiqid());
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('Function \'REQUIRE()\' is not callable by an adapter')
		;

		$adapter->md5 = 0;
		$adapter->md5[1] = 1;
		$adapter->md5[2] = 2;

		$this->assert
			->integer($adapter->md5())->isEqualTo(1)
			->integer($adapter->md5())->isEqualTo(2)
			->integer($adapter->md5())->isEqualTo(0)
		;

		$this->assert
			->integer($adapter->MD5())->isEqualTo(1)
			->integer($adapter->MD5())->isEqualTo(2)
			->integer($adapter->MD5())->isEqualTo(0)
		;

		$adapter->MD5 = 0;
		$adapter->MD5[1] = 1;
		$adapter->MD5[2] = 2;

		$this->assert
			->integer($adapter->md5())->isEqualTo(1)
			->integer($adapter->md5())->isEqualTo(2)
			->integer($adapter->md5())->isEqualTo(0)
		;

		$this->assert
			->integer($adapter->MD5())->isEqualTo(1)
			->integer($adapter->MD5())->isEqualTo(2)
			->integer($adapter->MD5())->isEqualTo(0)
		;
	}

	public function testGetCallsNumber()
	{
		$this->assert
			->integer(atoum\test\adapter::getCallsNumber())->isZero()
		;

		$adapter = new atoum\test\adapter();
		$adapter->md5(uniqid());

		$this->assert
			->integer(atoum\test\adapter::getCallsNumber())->isEqualTo(1)
		;

		$adapter->md5(uniqid());

		$this->assert
			->integer(atoum\test\adapter::getCallsNumber())->isEqualTo(2)
		;

		$otherAdapter = new atoum\test\adapter();
		$otherAdapter->sha1(uniqid());

		$this->assert
			->integer(atoum\test\adapter::getCallsNumber())->isEqualTo(3)
		;
	}

	public function testGetCalls()
	{
		$adapter = new atoum\test\adapter();

		$this->assert
			->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter, & $firstHash) { $adapter->md5($firstHash = uniqid()); })
				->array($adapter->getCalls())->isEqualTo(array('md5' => array(1 => array($firstHash))))
				->array($adapter->getCalls('md5'))->isEqualTo(array(1 => array($firstHash)))
				->array($adapter->getCalls('MD5'))->isEqualTo(array(1 => array($firstHash)))
			->when(function() use ($adapter, & $secondHash) { $adapter->md5($secondHash = uniqid()); })
				->array($adapter->getCalls())->isEqualTo(array('md5' => array(1 => array($firstHash), 2 => array($secondHash))))
				->array($adapter->getCalls('md5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash)))
				->array($adapter->getCalls('MD5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash)))
			->when(function() use ($adapter, & $thirdHash) {
					$adapter->md5 = function() {};
					$adapter->md5($thirdHash = uniqid());
				}
			)
				->array($adapter->getCalls())->isEqualTo(array('md5' => array(1 => array($firstHash), 2 => array($secondHash), 3 => array($thirdHash))))
				->array($adapter->getCalls('md5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash), 3 => array($thirdHash)))
				->array($adapter->getCalls('MD5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash), 3 => array($thirdHash)))
			->when(function() use ($adapter, & $haystack, & $needle, & $offset) {
					$haystack = uniqid();
					$needle = uniqid();
					$offset = rand(0, 12);

					$adapter->strpos($haystack, $needle, $offset);
				}
			)
				->array($adapter->getCalls())->isEqualTo(array(
							'md5' => array(
								1 => array($firstHash),
								2 => array($secondHash),
								3 => array($thirdHash)
							),
							'strpos' => array(
								4 => array($haystack, $needle, $offset)
							)
					)
				)
				->array($adapter->getCalls('md5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash), 3 => array($thirdHash)))
				->array($adapter->getCalls('MD5'))->isEqualTo(array(1 => array($firstHash), 2 => array($secondHash), 3 => array($thirdHash)))
				->array($adapter->getCalls('strpos'))->isEqualTo(array(4 => array($haystack, $needle, $offset)))
				->array($adapter->getCalls('STRPOS'))->isEqualTo(array(4 => array($haystack, $needle, $offset)))
		;
	}

	public function testResetCalls()
	{
		$adapter = new atoum\test\adapter();

		$this->assert
			->when(function() use ($adapter) { $adapter->md5(uniqid()); })
				->array($adapter->getCalls())->isNotEmpty()
				->object($adapter->resetCalls())->isIdenticalTo($adapter)
				->array($adapter->getCalls())->isEmpty()
		;
	}

	public function testReset()
	{
		$adapter = new atoum\test\adapter();

		$this->assert
			->array($adapter->getCallables())->isEmpty()
			->array($adapter->getCalls())->isEmpty()
			->object($adapter->reset())->isIdenticalTo($adapter)
			->array($adapter->getCallables())->isEmpty()
			->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) { $adapter->md5(uniqid()); })
				->array($adapter->getCallables())->isEmpty()
				->array($adapter->getCalls())->isNotEmpty()
				->object($adapter->reset())->isIdenticalTo($adapter)
				->array($adapter->getCallables())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) { $adapter->md5 = uniqid(); })
				->array($adapter->getCallables())->isNotEmpty()
				->array($adapter->getCalls())->isEmpty()
				->object($adapter->reset())->isIdenticalTo($adapter)
				->array($adapter->getCallables())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
			->when(function() use ($adapter) {
					$adapter->md5 = uniqid();
					$adapter->md5(uniqid());
				}
			)
				->array($adapter->getCallables())->isNotEmpty()
				->array($adapter->getCalls())->isNotEmpty()
				->object($adapter->reset())->isIdenticalTo($adapter)
				->array($adapter->getCallables())->isEmpty()
				->array($adapter->getCalls())->isEmpty()
		;
	}

	public function testAddCall()
	{
		$adapter = new atoum\test\adapter();

		$this->assert
			->array($adapter->getCalls())->isEmpty()
			->object($adapter->addCall($method = uniqid(), $args = array(uniqid())))->isIdenticalTo($adapter)
			->array($adapter->getCalls($method))->isEqualTo(array(1 => $args))
			->array($adapter->getCalls(strtoupper($method)))->isEqualTo(array(1 => $args))
		;
	}
}

?>
