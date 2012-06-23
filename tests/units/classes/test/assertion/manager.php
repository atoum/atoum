<?php

namespace mageekguy\atoum\tests\units\test\assertion;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\test\assertion
;

require_once __DIR__ . '/../../../runner.php';

class manager extends atoum\test
{
	public function test__construct()
	{
		$this->assert
			->if($assertionManager = new assertion\manager())
			->then
				->variable($assertionManager->getDefaultHandler())->isNull()
				->array($assertionManager->getHandlers())->isEmpty()
		;
	}

	public function test__get()
	{
		$this->assert
			->if($assertionManager = new assertion\manager())
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
		$this->assert
			->if($assertionManager = new assertion\manager())
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
		$this->assert
			->if($assertionManager = new assertion\manager())
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
		$this->assert
			->if($assertionManager = new assertion\manager())
			->then
				->object($assertionManager->setDefaultHandler($handler = function() {}))->isIdenticalTo($assertionManager)
				->object($assertionManager->getDefaultHandler())->isIdenticalTo($handler)
		;
	}

	public function testInvoke()
	{
		$this->assert
			->if($assertionManager = new assertion\manager())
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
