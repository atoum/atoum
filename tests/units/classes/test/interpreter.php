<?php

namespace mageekguy\atoum\tests\units\test;

use
	mageekguy\atoum,
	mageekguy\atoum\test
;

require_once __DIR__ . '/../../runner.php';

class interpreter extends atoum\test
{
	public function test__construct()
	{
		$this->assert
			->if($interpreter = new test\interpreter())
			->then
				->variable($interpreter->getDefaultHandler())->isNull()
				->array($interpreter->getHandlers())->isEmpty()
		;
	}

	public function test__get()
	{
		$this->assert
			->if($interpreter = new test\interpreter())
			->then
				->exception(function() use ($interpreter, & $event) {
						$interpreter->{$event = uniqid()};
					}
				)
					->isInstanceOf('mageekguy\atoum\test\interpreter\exception')
					->hasMessage('There is no handler defined for event \'' . $event . '\'')
			->if($interpreter->setDefaultHandler(function() use (& $defaultReturn) { return ($defaultReturn = uniqid()); }))
			->then
				->string($interpreter->{uniqid()})->isEqualTo($defaultReturn)
			->if($interpreter->setHandler($event = uniqid(), function() use (& $eventReturn) { return ($eventReturn = uniqid()); }))
			->then
				->string($interpreter->{$event})->isEqualTo($eventReturn)
		;
	}

	public function test__call()
	{
		$this->assert
			->if($interpreter = new test\interpreter())
			->then
				->exception(function() use ($interpreter, & $event) {
						$interpreter->{$event = uniqid()}();
					}
				)
					->isInstanceOf('mageekguy\atoum\test\interpreter\exception')
					->hasMessage('There is no handler defined for event \'' . $event . '\'')
			->if($interpreter->setDefaultHandler(function($event, $defaultArg) { return $defaultArg; }))
			->then
				->array($interpreter->{$event = uniqid()}($arg = uniqid()))->isEqualTo(array($arg))
			->if($interpreter->setHandler($event, function($arg) { return $arg; }))
			->then
				->string($interpreter->{$event}($eventArg = uniqid()))->isEqualTo($eventArg)
		;
	}

	public function testSetHandler()
	{
		$this->assert
			->if($interpreter = new test\interpreter())
			->then
				->object($interpreter->setHandler($event = uniqid(), $handler = function() {}))->isIdenticalTo($interpreter)
				->array($interpreter->getHandlers())->isEqualTo(array($event => $handler))
				->object($interpreter->setHandler($event, $otherHandler = function() {}))->isIdenticalTo($interpreter)
				->array($interpreter->getHandlers())->isEqualTo(array($event => $otherHandler))
				->object($interpreter->setHandler($otherEvent = uniqid(), $handler))->isIdenticalTo($interpreter)
				->array($interpreter->getHandlers())->isEqualTo(array($event => $otherHandler, $otherEvent => $handler))
		;
	}

	public function testSetDefaultHandler()
	{
		$this->assert
			->if($interpreter = new test\interpreter())
			->then
				->object($interpreter->setDefaultHandler($handler = function() {}))->isIdenticalTo($interpreter)
				->object($interpreter->getDefaultHandler())->isIdenticalTo($handler)
		;
	}

	public function testInvoke()
	{
		$this->assert
			->if($interpreter = new test\interpreter())
			->then
				->exception(function() use ($interpreter, & $event) {
						$interpreter->invoke($event = uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\test\interpreter\exception')
					->hasMessage('There is no handler defined for event \'' . $event . '\'')
			->if($interpreter->setDefaultHandler(function($event, $arg) { return $arg; }))
			->then
				->array($interpreter->invoke(uniqid(), array($defaultArg = uniqid())))->isEqualTo(array($defaultArg))
			->if($interpreter->setHandler($event = uniqid(), function($eventArg) { return $eventArg; }))
			->then
				->string($interpreter->invoke($event, array($eventArg = uniqid())))->isEqualTo($eventArg)
		;
	}
}

?>
