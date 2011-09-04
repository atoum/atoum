<?php

namespace mageekguy\atoum\tests\units\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter
;

require_once __DIR__ . '/../../runner.php';

class generator extends atoum\test
{
	public function test__construct()
	{
		$generator = new asserter\generator($this);

		$this->assert
			->object($generator->getTest())->isIdenticalTo($this)
			->object($generator->getLocale())->isIdenticalTo($this->getLocale())
		;
	}

	public function test__call()
	{
		$generator = new asserter\generator($this, $locale = new atoum\locale());

		$this->assert
			->exception(function() use ($generator, & $asserter) {
					$generator->{$asserter = uniqid()}();
				}
			)
			->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
			->hasMessage('Asserter \'mageekguy\atoum\asserters\\' . $asserter . '\' does not exist')
		;

		$this->assert
			->object($generator->variable(uniqid()))->isInstanceOf('mageekguy\atoum\asserters\variable')
		;
	}

	public function testWhen()
	{
		$generator = new asserter\generator($this);

		$value = null;

		$this->assert
			->variable($value)->isNull()
			->object($generator->when(function() use (& $value) { $value = uniqid(); }))->isIdenticalTo($generator)
			->variable($value)->isNotNull()
		;
	}

	public function testSetTest()
	{
		$generator = new asserter\generator($this);

		$this->assert
			->object($generator->setTest($test = new self()))->isIdenticalTo($generator)
			->object($generator->getTest())->isIdenticalTo($test)
			->object($generator->getLocale())->isIdenticalTo($test->getLocale())
		;
	}

	public function testSetAlias()
	{
		$generator = new asserter\generator($this);

		$this->assert
			->object($generator->setAlias($alias = uniqid(), $asserter = uniqid()))->isIdenticalTo($generator)
			->array($generator->getAliases())->isEqualTo(array($alias => $asserter))
		;
	}

	public function testResetAliases()
	{
		$generator = new asserter\generator($this, $locale = new atoum\locale());

		$generator->setAlias(uniqid(), uniqid());

		$this->assert
			->array($generator->getAliases())->isNotEmpty()
			->object($generator->resetAliases())->isIdenticalTo($generator)
			->array($generator->getAliases())->isEmpty()
		;
	}
}

?>
