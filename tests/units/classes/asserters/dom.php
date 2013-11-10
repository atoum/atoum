<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters\dom as sut
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
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->object($asserter->getLocale())->isIdenticalTo($generator->getLocale())
				->object($asserter->getGenerator())->isIdenticalTo($generator)
				->variable($asserter->getValue())->isNull()
				->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function test__get()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->toString; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
				->exception(function() use ($asserter, & $property) { $asserter->{$property = uniqid()}; })
					->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
					->hasMessage('Asserter \'' . $property . '\' does not exist')
			->if($asserter->setWith(new \DOMDocument()))
			->then
				->object($asserter->toString)->isInstanceOf('mageekguy\atoum\asserters\castToString')
		;
	}

	public function testSetWith()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter, & $value) { $asserter->setWith($value = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not a DOM node'), $asserter->getTypeOf($value)))
				->string($asserter->getValue())->isEqualTo($value)
				->object($asserter->setWith($value = new \DOMDocument()))->isIdenticalTo($asserter)
				->object($asserter->getValue())->isIdenticalTo($value)
				->object($asserter->setWith($value = uniqid(), false))->isIdenticalTo($asserter)
				->string($asserter->getValue())->isEqualTo($value)
		;
	}

	public function testHasSize()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($document = new \DOMDocument())
			->and($document->appendChild($document->createElement('a' . uniqid())))
			->and($asserter->setWith($document))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(0); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s has size %d, expected size %d'), $asserter, sizeof($document->childNodes), 0))
				->object($asserter->hasSize(sizeof($document->childNodes)))->isIdenticalTo($asserter);
		;
	}

	public function testIsEmpty()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->hasSize(rand(0, PHP_INT_MAX)); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($document = new \DOMDocument())
			->and($document->appendChild($document->createElement('a' . uniqid())))
			->and($asserter->setWith($document))
			->then
				->exception(function() use ($asserter) { $asserter->isEmpty(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not empty'), $asserter, sizeof($document->childNodes)))
			->if($document = new \DOMDocument())
			->and($asserter->setWith(new \DOMDocument()))
			->then
				->object($asserter->isEmpty())->isIdenticalTo($asserter)
		;
	}

	public function testIsCloneOf()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->isCloneOf($asserter); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($asserter->setWith($document = new \DOMDocument()))
			->then
				->exception(function() use ($asserter, $document) { $asserter->isCloneOf($document); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not a clone of %s'), $asserter, $asserter->getTypeOf($document)))
			->if($clonedDocument = clone $document)
			->then
				->object($asserter->isCloneOf($clonedDocument))->isIdenticalTo($asserter)
		;
	}

	public function testToString()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) { $asserter->toString(); })
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($asserter->setWith(new \DOMDocument()))
			->then
				->object($asserter->toString())->isInstanceOf('mageekguy\atoum\asserters\castToString')
		;
	}

	public function testIsInstanceOf()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) {
						$asserter->isInstanceOf($asserter);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($asserter->setWith($document = new \DOMDocument()))
			->then
				->exception(function() use ($asserter) {
						$asserter->isInstanceOf($asserter);
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is not an instance of %s'), $asserter->getTypeOf($document), $asserter->getTypeOf($asserter)))
				->object($asserter->isInstanceOf($document))->isIdenticalTo($asserter)
		;
	}

	public function testIsNotInstanceOf()
	{
		$this
			->if($asserter = new sut($generator = new asserter\generator()))
			->then
				->exception(function() use ($asserter) {
						$asserter->isNotInstanceOf($asserter);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('DOM node is undefined')
			->if($asserter->setWith($document = new \DOMDocument()))
			->then
				->exception(function() use ($asserter, $document) {
						$asserter->isNotInstanceOf($document);
					}
				)
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s is an instance of %1$s'), $asserter->getTypeOf($document)))
				->object($asserter->isNotInstanceOf($asserter))->isIdenticalTo($asserter)
		;
	}
}
