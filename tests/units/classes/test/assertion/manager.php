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

			->if($assertionManager->{$event = uniqid()} = function() use (& $return) { return ($return = uniqid()); })
			->then
				->string($assertionManager->invokeMethodHandler($event))->isEqualTo($return)
				->string($assertionManager->invokePropertyHandler($event))->isEqualTo($return)
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
				->object($assertionManager->setHandler($event = uniqid(), function() use (& $return) { return ($return = uniqid()); }))->isIdenticalTo($assertionManager)
				->string($assertionManager->invokeMethodHandler($event))->isEqualTo($return)
				->string($assertionManager->invokePropertyHandler($event))->isEqualTo($return)
				->object($assertionManager->setHandler($otherEvent = uniqid(), function() use (& $otherReturn) { return ($otherReturn = uniqid()); }))->isIdenticalTo($assertionManager)
				->string($assertionManager->invokeMethodHandler($event))->isEqualTo($return)
				->string($assertionManager->invokePropertyHandler($event))->isEqualTo($return)
				->string($assertionManager->invokeMethodHandler($otherEvent))->isEqualTo($otherReturn)
				->string($assertionManager->invokePropertyHandler($otherEvent))->isEqualTo($otherReturn)
		;
	}

	public function testSetPropertyHandler()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->object($assertionManager->setPropertyHandler($event = uniqid(), function() use (& $return) { return ($return = uniqid()); }))->isIdenticalTo($assertionManager)
				->string($assertionManager->invokePropertyHandler($event))->isEqualTo($return)
				->exception(function() use ($assertionManager, $event) {
						$assertionManager->invokeMethodHandler($event);
					}
				)
					->isInstanceOf('mageekguy\atoum\test\assertion\manager\exception')
					->hasMessage('There is no handler defined for event \'' . $event . '\'')
				->object($assertionManager->setPropertyHandler($otherEvent = uniqid(), function() use (& $otherReturn) { return ($otherReturn = uniqid()); }))->isIdenticalTo($assertionManager)
				->string($assertionManager->invokePropertyHandler($event))->isEqualTo($return)
				->string($assertionManager->invokePropertyHandler($otherEvent))->isEqualTo($otherReturn)
		;
	}

	public function testSetMethodHandler()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->object($assertionManager->setMethodHandler($event = uniqid(), function() use (& $return) { return ($return = uniqid()); }))->isIdenticalTo($assertionManager)
				->exception(function() use ($assertionManager, $event) {
						$assertionManager->invokePropertyHandler($event);
					}
				)
					->isInstanceOf('mageekguy\atoum\test\assertion\manager\exception')
					->hasMessage('There is no handler defined for event \'' . $event . '\'')
				->string($assertionManager->invokeMethodHandler($event))->isEqualTo($return)
				->object($assertionManager->setMethodHandler($otherEvent = uniqid(), function() use (& $otherReturn) { return ($otherReturn = uniqid()); }))->isIdenticalTo($assertionManager)
				->string($assertionManager->invokeMethodHandler($event))->isEqualTo($return)
				->string($assertionManager->invokeMethodHandler($otherEvent))->isEqualTo($otherReturn)
		;
	}

	public function testSetDefaultHandler()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->object($assertionManager->setDefaultHandler($handler = function() {}))->isIdenticalTo($assertionManager)
		;
	}

	public function testInvokeMethodHandler()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->exception(function() use ($assertionManager, & $event) {
						$assertionManager->invokeMethodHandler($event = uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\test\assertion\manager\exception')
					->hasMessage('There is no handler defined for event \'' . $event . '\'')
			->if($assertionManager->setDefaultHandler(function($event, $arg) { return $arg; }))
			->then
				->array($assertionManager->invokeMethodHandler(uniqid(), array($defaultArg = uniqid())))->isEqualTo(array($defaultArg))
			->if($assertionManager->setMethodHandler($event = uniqid(), function($eventArg) { return $eventArg; }))
			->then
				->string($assertionManager->invokeMethodHandler($event, array($eventArg = uniqid())))->isEqualTo($eventArg)
		;
	}
}
