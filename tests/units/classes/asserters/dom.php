<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter
;

require_once __DIR__ . '/../../runner.php';

class dom extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\object');
	}

	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->getLocale())->isEqualTo(new atoum\locale())
				->object($this->testedInstance->getGenerator())->isEqualTo(new asserter\generator())
				->variable($this->testedInstance->getValue())->isNull()
				->boolean($this->testedInstance->wasSet())->isFalse()
		;
	}

	public function test__get()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->exception(function($test) { $test->testedInstance->toString; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
				->exception(function($test) use (& $property) { $test->testedInstance->{$property = uniqid()}; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $property . '\' does not exist')
			->if($this->testedInstance->setWith(new \DOMDocument()))
			->then
				->object($this->testedInstance->toString)->isInstanceOf('mageekguy\atoum\asserters\castToString')
		;
	}

	public function testSetWith()
	{
		$this
			->if(
				$this->newTestedInstance
					->setlocale($locale = new atoum\locale())
			)
			->then
				->exception(function($test) use (& $value) { $test->testedInstance->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($locale->_('%s is not a DOM node'), $this->testedInstance->getAnalyzer()->getTypeOf($value)))
				->string($this->testedInstance->getValue())->isEqualTo($value)
				->object($this->testedInstance->setWith($value = new \DOMDocument()))->isTestedInstance
				->object($this->testedInstance->getValue())->isIdenticalTo($value)
				->object($this->testedInstance->setWith($value = uniqid(), false))->isTestedInstance
				->string($this->testedInstance->getValue())->isEqualTo($value)
		;
	}

	public function testHasSize()
	{
		$this
			->if(
				$this->newTestedInstance
					->setlocale($locale = new atoum\locale())
			)
			->then
				->exception(function($test) { $test->testedInstance->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($document = new \DOMDocument())
			->and($document->appendChild($document->createElement('a' . uniqid())))
			->and($this->testedInstance->setWith($document))
			->then
				->exception(function($test) { $test->testedInstance->hasSize(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($locale->_('%s has size %d, expected size %d'), $this->testedInstance, sizeof($document->childNodes), 0))
				->object($this->testedInstance->hasSize(sizeof($document->childNodes)))->isTestedInstance
		;
	}

	public function testIsEmpty()
	{
		$this
			->if(
				$this->newTestedInstance
					->setlocale($locale = new atoum\locale())
			)
			->then
				->exception(function($test) { $test->testedInstance->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($document = new \DOMDocument())
			->and($document->appendChild($document->createElement('a' . uniqid())))
			->and($this->testedInstance->setWith($document))
			->then
				->exception(function($test) { $test->testedInstance->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($locale->_('%s is not empty'), $this->testedInstance, sizeof($document->childNodes)))
			->if($document = new \DOMDocument())
			->and($this->testedInstance->setWith(new \DOMDocument()))
			->then
				->object($this->testedInstance->isEmpty())->isTestedInstance
		;
	}

	public function testIsCloneOf()
	{
		$this
			->if(
				$this->newTestedInstance
					->setlocale($locale = new atoum\locale())
			)
			->then
				->exception(function($test) { $test->testedInstance->isCloneOf($test->testedInstance); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($this->testedInstance->setWith($document = new \DOMDocument()))
			->then
				->exception(function($test) use ($document) { $test->testedInstance->isCloneOf($document); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($locale->_('%s is not a clone of %s'), $this->testedInstance, $this->testedInstance->getAnalyzer()->getTypeOf($document)))
			->if($clonedDocument = clone $document)
			->then
				->object($this->testedInstance->isCloneOf($clonedDocument))->isTestedInstance
		;
	}

	public function testToString()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->exception(function($test) { $test->testedInstance->toString(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($this->testedInstance->setWith(new \DOMDocument()))
			->then
				->object($this->testedInstance->toString())->isInstanceOf('mageekguy\atoum\asserters\castToString')
		;
	}

	public function testIsInstanceOf()
	{
		$this
			->if(
				$this->newTestedInstance
					->setlocale($locale = new atoum\locale())
			)
			->then
				->exception(function($test) {
						$test->testedInstance->isInstanceOf($test->testedInstance);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($this->testedInstance->setWith($document = new \DOMDocument()))
			->then
				->exception(function($test) {
						$test->testedInstance->isInstanceOf($test->testedInstance);
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($locale->_('%s is not an instance of %s'), $this->testedInstance->getAnalyzer()->getTypeOf($document), $this->testedInstance->getAnalyzer()->getTypeOf($this->testedInstance)))
				->object($this->testedInstance->isInstanceOf($document))->isTestedInstance
		;
	}

	public function testIsNotInstanceOf()
	{
		$this
			->if(
				$this->newTestedInstance
					->setlocale($locale = new atoum\locale())
			)
			->then
				->exception(function($test) {
						$test->testedInstance->isNotInstanceOf($test->testedInstance);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($this->testedInstance->setWith($document = new \DOMDocument()))
			->then
				->exception(function($test) use ($document) {
						$test->testedInstance->isNotInstanceOf($document);
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($locale->_('%s is an instance of %1$s'), $this->testedInstance->getAnalyzer()->getTypeOf($document)))
				->object($this->testedInstance->isNotInstanceOf($this->testedInstance))->isTestedInstance
		;
	}
}
