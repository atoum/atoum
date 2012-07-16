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
				->string($assertionManager->{$event})->isEqualTo($eventReturn)
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
				->array($assertionManager->{$event = uniqid()}($arg = uniqid()))->isEqualTo(array($arg))
			->if($assertionManager->setHandler($event, function($arg) { return $arg; }))
			->then
				->string($assertionManager->{$event}($eventArg = uniqid()))->isEqualTo($eventArg)
		;
	}

	public function testSetHandler()
	{
		$this
			->if($assertionManager = new testedClass())
			->then
				->object($assertionManager->setHandler($event = uniqid(), $handler = function() {}))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => $handler))
				->object($assertionManager->setHandler($event, $otherHandler = function() {}))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => $otherHandler))
				->object($assertionManager->setHandler($otherEvent = uniqid(), $handler))->isIdenticalTo($assertionManager)
				->array($assertionManager->getHandlers())->isEqualTo(array($event => $otherHandler, $otherEvent => $handler))
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
