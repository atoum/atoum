<?php

namespace mageekguy\atoum\tests\units;

use mageekguy\atoum;

require_once(__DIR__ . '/../runner.php');

/** @isolation off */
class adapter extends atoum\test
{
	public function test__set()
	{
		$adapter = new atoum\adapter();

		$closure = function() {};

		$adapter->md5 = $closure;

		$this->assert
			->variable($adapter->md5)->isIdenticalTo($closure)
		;
	}

	public function test__get()
	{
		$adapter = new atoum\adapter();

		$closure = function() {};

		$adapter->md5 = $closure;

		$this->assert
			->boolean(isset($adapter->md5))->isTrue()
			->variable($adapter->md5)->isIdenticalTo($closure)
		;
	}

	public function test__isset()
	{
		$adapter = new atoum\adapter();

		$this->assert
			->boolean(isset($adapter->md5))->isFalse()
		;

		$adapter->md5 = function() {};

		$this->assert
			->boolean(isset($adapter->md5))->isTrue()
		;
	}

	public function test__call()
	{
		$adapter = new atoum\adapter();

		$hash = uniqid();

		$this->assert->string($adapter->md5($hash))->isEqualTo(md5($hash));

		$md5 = uniqid();

		$adapter->md5 = function() use ($md5) { return $md5; };

		$this->assert->string($adapter->md5($hash))->isEqualTo($md5);
	}

	public function testGetCalls()
	{
		$adapter = new atoum\adapter();

		$this->assert->collection($adapter->getCalls())->isEmpty();

		$firstHash = uniqid();

		$adapter->md5($firstHash);

		$this->assert
			->collection($adapter->getCalls())->isEqualTo(array('md5' => array(array($firstHash))))
			->collection($adapter->getCalls('md5'))->isEqualTo(array(array($firstHash)))
		;

		$secondHash = uniqid();
		$adapter->md5($secondHash);

		$this->assert
			->collection($adapter->getCalls())->isEqualTo(array('md5' => array(array($firstHash), array($secondHash))))
			->collection($adapter->getCalls('md5'))->isEqualTo(array(array($firstHash), array($secondHash)))
		;

		$adapter->md5 = function() {};

		$thirdHash = uniqid();
		$adapter->md5($thirdHash);

		$this->assert
			->collection($adapter->getCalls())->isEqualTo(array('md5' => array(array($firstHash), array($secondHash), array($thirdHash))))
			->collection($adapter->getCalls('md5'))->isEqualTo(array(array($firstHash), array($secondHash), array($thirdHash)))
		;

		$haystack = uniqid();
		$needle = uniqid();
		$offset = rand(0, 12);

		$adapter->strpos($haystack, $needle, $offset);

		$this->assert
			->collection($adapter->getCalls())->isEqualTo(array(
						'md5' => array(
							array($firstHash),
							array($secondHash),
							array($thirdHash)
						),
						'strpos' => array(
							array($haystack, $needle, $offset)
						)
				)
			)
			->collection($adapter->getCalls('md5'))->isEqualTo(array(array($firstHash), array($secondHash), array($thirdHash)))
			->collection($adapter->getCalls('strpos'))->isEqualTo(array(array($haystack, $needle, $offset)))
		;
	}
}

?>
