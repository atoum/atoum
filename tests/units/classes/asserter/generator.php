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
		$this->assert
			->if($generator = new asserter\generator())
			->then
				->variable($generator->getTest())->isNull()
				->object($generator->getLocale())->isEqualTo(new atoum\locale())
			->if($generator = new asserter\generator($this))
			->then
				->object($generator->getTest())->isIdenticalTo($this)
				->object($generator->getLocale())->isIdenticalTo($this->getLocale())
			->if($generator = new asserter\generator($this, $locale = new atoum\locale()))
			->then
				->object($generator->getTest())->isIdenticalTo($this)
				->object($generator->getLocale())->isIdenticalTo($locale)
		;
	}

	public function test__get()
	{
		$this->assert
			->if($generator = new asserter\generator())
			->then
				->exception(function() use ($generator, & $asserter) {
						$generator->{$asserter = uniqid()};
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $asserter . '\' does not exist')
				->object($generator->variable)->isInstanceOf('mageekguy\atoum\asserters\variable')
				->variable($this->getScore()->getCase())->isNull()
			->if($generator = new asserter\generator($this))
			->then
				->exception(function() use ($generator, & $asserter) {
						$generator->{$asserter = uniqid()};
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $asserter . '\' does not exist')
				->object($generator->variable)->isInstanceOf('mageekguy\atoum\asserters\variable')
				->variable($this->getScore()->getCase())->isNull()
		;
	}

	public function test__call()
	{
		$this->assert
			->if($generator = new asserter\generator())
			->then
				->exception(function() use ($generator, & $asserter) {
						$generator->{$asserter = uniqid()}();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $asserter . '\' does not exist')
				->object($generator->variable(uniqid()))->isInstanceOf('mageekguy\atoum\asserters\variable')
				->variable($this->getScore()->getCase())->isNull()
			->if($generator = new asserter\generator($this))
			->then
				->exception(function() use ($generator, & $asserter) {
						$generator->{$asserter = uniqid()}();
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $asserter . '\' does not exist')
				->object($generator->variable(uniqid()))->isInstanceOf('mageekguy\atoum\asserters\variable')
		;
	}

	public function testGetScore()
	{
		$this->assert
			->if($generator = new asserter\generator())
			->then
				->variable($generator->getScore())->isNull()
			->if($generator = new asserter\generator($this))
			->then
				->object($generator->getScore())->isIdenticalTo($this->getScore())
		;
	}

	public function testSetLocale()
	{
		$this->assert
			->if($generator = new asserter\generator())
			->then
				->object($generator->setLocale($locale = new atoum\locale()))->isIdenticalTo($generator)
				->object($generator->getLocale())->isIdenticalTo($locale)
			->if($generator = new asserter\generator($this))
			->then
				->object($generator->setLocale($locale = new atoum\locale()))->isIdenticalTo($generator)
				->object($generator->getLocale())->isIdenticalTo($locale)
			->if($generator = new asserter\generator($this, new atoum\locale))
			->then
				->object($generator->setLocale($locale = new atoum\locale()))->isIdenticalTo($generator)
				->object($generator->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetTest()
	{
		$this->assert
			->if($generator = new asserter\generator())
			->then
				->object($generator->setTest($this))->isIdenticalTo($generator)
				->object($generator->getTest())->isIdenticalTo($this)
				->object($generator->getLocale())->isIdenticalTo($this->getLocale())
			->if($generator = new asserter\generator($this))
			->then
				->object($generator->setTest($test = new self()))->isIdenticalTo($generator)
				->object($generator->getTest())->isIdenticalTo($test)
				->object($generator->getLocale())->isIdenticalTo($test->getLocale())
		;
	}

	public function testSetAlias()
	{
		$this->assert
			->if($generator = new asserter\generator())
			->then
				->object($generator->setAlias($alias = uniqid(), $asserter = uniqid()))->isIdenticalTo($generator)
				->array($generator->getAliases())->isEqualTo(array($alias => $asserter))
			->if($generator = new asserter\generator($this))
			->then
				->object($generator->setAlias($alias = uniqid(), $asserter = uniqid()))->isIdenticalTo($generator)
				->array($generator->getAliases())->isEqualTo(array($alias => $asserter))
		;
	}

	public function testResetAliases()
	{
		$this->assert
			->if($generator = new asserter\generator())
			->and($generator->setAlias(uniqid(), uniqid()))
			->then
				->array($generator->getAliases())->isNotEmpty()
				->object($generator->resetAliases())->isIdenticalTo($generator)
				->array($generator->getAliases())->isEmpty()
			->if($generator = new asserter\generator($this))
			->and($generator->setAlias(uniqid(), uniqid()))
			->then
				->array($generator->getAliases())->isNotEmpty()
				->object($generator->resetAliases())->isIdenticalTo($generator)
				->array($generator->getAliases())->isEmpty()
		;
	}
}

?>
