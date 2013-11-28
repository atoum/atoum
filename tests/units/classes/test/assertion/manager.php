<?php

namespace mageekguy\atoum\tests\units\test\assertion;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\test\assertion\manager as testedClass
;

require_once __DIR__ . '/../../../runner.php';

class manager extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->variable($assertionManager->getDefaultHandler())->isNull()
				->array($assertionManager->getHandlers())->isEmpty()
		;
	}

	public function test__get()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->exception(function() use ($assertionManager, & $event) {
						$assertionManager->{$event = uniqid()};
					}
				)
					->isInstanceOf('mageekguy\atoum\test\assertion\manager\exception')
					->hasMessage('There is no handler defined for event \'' . $event . '\'')
			->if($assertionManager->setDefaultHandler(function() use (& $defaultReturn) { return ($defaultReturn = uniqid()); }))
			->then
				->string($assertionManager->{uniqid()})->isEqualTo($defaultReturn)
			->if($assertionManager->setHandler($event = uniqid(), function() use (& $eventReturn) { return ($eventReturn = uniqid()); }))
			->then
				->string($assertionManager->{uniqid()})->isEqualTo($defaultReturn)
				->string($assertionManager->{$event})->isEqualTo($eventReturn)
			->if($assertionManager->setMethodHandler($methodEvent = uniqid(), function() use (& $methodReturn) { return ($methodReturn = uniqid()); }))
			->then
				->string($assertionManager->{uniqid()})->isEqualTo($defaultReturn)
				->string($assertionManager->{$event})->isEqualTo($eventReturn)
				->string($assertionManager->{$methodEvent})->isEqualTo($defaultReturn)
			->if($assertionManager->setPropertyHandler($propertyEvent = uniqid(), function() use (& $propertyReturn) { return ($propertyReturn = uniqid()); }))
			->then
				->string($assertionManager->{uniqid()})->isEqualTo($defaultReturn)
				->string($assertionManager->{$event})->isEqualTo($eventReturn)
				->string($assertionManager->{$methodEvent})->isEqualTo($defaultReturn)
				->string($assertionManager->{$propertyEvent})->isEqualTo($propertyReturn)
		;
	}

	public function test__set()
	{
		$this
			->given($assertionManager = new testedClass())

			->if($assertionManager->{$event = uniqid()} = $handler = function() {})
			->then
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($handler, testedClass::propertyAndMethodHandler)))

			->if($assertionManager->{$event} = $otherHandler = function() {})
			->then
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($otherHandler, testedClass::propertyAndMethodHandler)))

			->if($assertionManager->{$otherEvent = uniqid()} = $handler)
			->then
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($otherHandler, testedClass::propertyAndMethodHandler), $otherEvent => array($handler, testedClass::propertyAndMethodHandler)))
		;
	}

	public function test__call()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->exception(function() use ($assertionManager, & $event) {
						$assertionManager->{$event = uniqid()}();
					}
				)
					->isInstanceOf('mageekguy\atoum\test\assertion\manager\exception')
					->hasMessage('There is no handler defined for event \'' . $event . '\'')
			->if($assertionManager->setDefaultHandler(function($event, $defaultArg) { return $defaultArg; }))
			->then
				->array($assertionManager->{uniqid()}($arg = uniqid()))->isEqualTo(array($arg))
			->if($assertionManager->setHandler($event, function($arg) { return $arg; }))
			->then
				->array($assertionManager->{uniqid()}($arg = uniqid()))->isEqualTo(array($arg))
				->string($assertionManager->{$event}($eventArg = uniqid()))->isEqualTo($eventArg)
			->if($assertionManager->setMethodHandler($methodEvent = uniqid(), function() use (& $methodReturn) { return ($methodReturn = uniqid()); }))
			->then
				->array($assertionManager->{uniqid()}($arg = uniqid()))->isEqualTo(array($arg))
				->string($assertionManager->{$event}($eventArg = uniqid()))->isEqualTo($eventArg)
				->string($assertionManager->{$methodEvent}())->isEqualTo($methodReturn)
			->if($assertionManager->setPropertyHandler($propertyEvent = uniqid(), function() use (& $propertyReturn) { return ($propertyReturn = uniqid()); }))
			->then
				->array($assertionManager->{uniqid()}($arg = uniqid()))->isEqualTo(array($arg))
				->string($assertionManager->{$event}($eventArg = uniqid()))->isEqualTo($eventArg)
				->string($assertionManager->{$methodEvent}())->isEqualTo($methodReturn)
				->array($assertionManager->{$propertyEvent}($arg = uniqid()))->isEqualTo(array($arg))
		;
	}

	public function testSetHandler()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->object($assertionManager->setHandler($event = uniqid(), $handler = function() {}))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($handler, testedClass::propertyAndMethodHandler)))
				->object($assertionManager->setHandler($event, $otherHandler = function() {}))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($otherHandler, testedClass::propertyAndMethodHandler)))
				->object($assertionManager->setHandler($otherEvent = uniqid(), $handler))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($otherHandler, testedClass::propertyAndMethodHandler), $otherEvent => array($handler, testedClass::propertyAndMethodHandler)))
		;
	}

	public function testSetPropertyHandler()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->object($assertionManager->setPropertyHandler($event = uniqid(), $handler = function() {}))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($handler, testedClass::propertyHandler)))
				->object($assertionManager->setPropertyHandler($event, $otherHandler = function() {}))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($otherHandler, testedClass::propertyHandler)))
				->object($assertionManager->setPropertyHandler($otherEvent = uniqid(), $handler))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($otherHandler, testedClass::propertyHandler), $otherEvent => array($handler, testedClass::propertyHandler)))
		;
	}

	public function testSetMethodHandler()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->object($assertionManager->setMethodHandler($event = uniqid(), $handler = function() {}))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($handler, testedClass::methodHandler)))
				->object($assertionManager->setMethodHandler($event, $otherHandler = function() {}))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($otherHandler, testedClass::methodHandler)))
				->object($assertionManager->setMethodHandler($otherEvent = uniqid(), $handler))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => array($otherHandler, testedClass::methodHandler), $otherEvent => array($handler, testedClass::methodHandler)))
		;
	}

	public function testSetDefaultHandler()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->object($assertionManager->setDefaultHandler($handler = function() {}))->isIdenticalTo($assertionManager)
				->object($assertionManager->getDefaultHandler())->isIdenticalTo($handler)
		;
	}

	public function testInvoke()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->exception(function() use ($assertionManager, & $event) {
						$assertionManager->invoke($event = uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\test\assertion\manager\exception')
					->hasMessage('There is no handler defined for event \'' . $event . '\'')
			->if($assertionManager->setDefaultHandler(function($event, $arg) { return $arg; }))
			->then
				->array($assertionManager->invoke(uniqid(), array($defaultArg = uniqid())))->isEqualTo(array($defaultArg))
			->if($assertionManager->setHandler($event = uniqid(), function($eventArg) { return $eventArg; }))
			->then
				->string($assertionManager->invoke($event, array($eventArg = uniqid())))->isEqualTo($eventArg)
		;
	}
}
