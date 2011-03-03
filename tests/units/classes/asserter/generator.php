<?php

namespace mageekguy\atoum\tests\units\asserter;

use mageekguy\atoum;
use mageekguy\atoum\asserter;

require_once(__DIR__ . '/../../runner.php');

class generator extends atoum\test
{
	public function test__construct()
	{
		$generator = new asserter\generator($this);

		$this->assert
			->object($generator->getTest())->isIdenticalTo($this)
			->object($generator->getLocale())->isInstanceOf('\mageekguy\atoum\locale')
		;

		$generator = new asserter\generator($this, $locale = new atoum\locale());

		$this->assert
			->object($generator->getTest())->isIdenticalTo($this)
			->object($generator->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetTest()
	{
		$generator = new asserter\generator(new self(), new atoum\locale());

		$this->assert
			->object($generator->getTest())->isNotIdenticalTo($this)
			->object($generator->setTest($this))->isIdenticalTo($generator)
			->object($generator->getTest())->isIdenticalTo($this)
		;
	}

	public function testSetLocale()
	{
		$generator = new asserter\generator($this, new atoum\locale());

		$this->assert
			->object($generator->setLocale($locale = new atoum\locale()))->isIdenticalTo($generator)
			->object($generator->getLocale())->isIdenticalTo($locale)
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

	public function testSetLabel()
	{
		$generator = new asserter\generator($this);

		$asserter = new atoum\asserters\adapter(new atoum\score(), new atoum\locale(), $generator);

		$asserter->setWith($adapter = new atoum\adapter());

		$this->assert
			->array($generator->getLabels())->isEmpty()
			->object($generator->setLabel($label = uniqid(), $asserter))->isIdenticalTo($generator)
			->array($generator->getLabels())->isEqualTo(array($label => $asserter))
			->object($generator->{$label})->isNotIdenticalTo($asserter)
			->object($generator->{$label}->getAdapter())->isIdenticalTo($adapter)
			->exception(function() use ($generator, $label, $asserter) {
						$generator->setLabel($label, $asserter);
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Label \'' . $label . '\' is already defined')
			->exception(function() use ($generator, $asserter) {
						$generator->setLabel('adapter', $asserter);
					}
				)
					->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Unable to use \'adapter\' as label because there is an asserter with this name')
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
			->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
			->hasMessage('Asserter \'mageekguy\atoum\asserters\\' . $asserter . '\' does not exist')
		;

		$this->assert
			->object($generator->variable(uniqid()))->isInstanceOf('mageekguy\atoum\asserters\variable')
		;
	}
}

?>
