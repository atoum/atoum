<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\duration;

use
	mageekguy\atoum,
	mageekguy\atoum\runner,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner\duration\phing as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

class phing extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\duration\cli') ;
	}

	public function test__construct()
	{
		$this
			->if($field = new testedClass())
			->then
				->object($field->getPrompt())->isEqualTo(new prompt())
				->object($field->getTitleColorizer())->isEqualTo(new colorizer())
				->object($field->getDurationColorizer())->isEqualTo(new colorizer())
				->object($field->getLocale())->isEqualTo(new locale())
				->variable($field->getValue())->isNull()
				->array($field->getEvents())->isEqualTo(array(runner::runStop))
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($field = new testedClass())
			->then
				->object($field->setPrompt($prompt = new prompt(uniqid())))->isIdenticalTo($field)
				->object($field->getPrompt())->isIdenticalTo($prompt)
				->object($field->setPrompt())->isIdenticalTo($field)
				->object($field->getPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo(new prompt())
		;
	}

	public function testSetTitleColorizer()
	{
		$this
			->if($field = new testedClass())
			->then
				->object($field->setTitleColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getTitleColorizer())->isIdenticalTo($colorizer)
				->object($field->setTitleColorizer())->isIdenticalTo($field)
				->object($field->getTitleColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testSetDurationColorizer()
	{
		$this
			->if($field = new testedClass())
			->then
				->object($field->setDurationColorizer($colorizer = new colorizer()))->isIdenticalTo($field)
				->object($field->getDurationColorizer())->isIdenticalTo($colorizer)
				->object($field->setDurationColorizer())->isIdenticalTo($field)
				->object($field->getDurationColorizer())
					->isNotIdenticalTo($colorizer)
					->isEqualTo(new colorizer())
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($field = new testedClass())
			->then
				->boolean($field->handleEvent(runner::runStart, new atoum\runner()))->isFalse()
				->variable($field->getValue())->isNull()
			->if($runner = new \mock\mageekguy\atoum\runner())
			->and($runner->getMockController()->getRunningDuration = $runningDuration = rand(0, PHP_INT_MAX))
			->then
				->boolean($field->handleEvent(runner::runStop, $runner))->isTrue()
				->integer($field->getValue())->isEqualTo($runningDuration)
		;
	}

	public function test__toString()
	{
		$this
			->if($runner = new \mock\mageekguy\atoum\runner())
			->and($runner->getMockController()->getRunningDuration = 1)
			->and($prompt = new \mock\mageekguy\atoum\cli\prompt())
			->and($prompt->getMockController()->__toString = $promptString = uniqid())
			->and($titleColorizer = new \mock\mageekguy\atoum\cli\colorizer())
			->and($titleColorizer->getMockController()->colorize = $colorizedTitle = uniqid())
			->and($durationColorizer = new \mock\mageekguy\atoum\cli\colorizer())
			->and($durationColorizer->getMockController()->colorize = $colorizedDuration = uniqid())
			->and($locale = new \mock\mageekguy\atoum\locale())
			->and($locale->getMockController()->_ = function($string) { return $string; })
			->and($field = new testedClass())
			->and($field->setPrompt($prompt))
			->and($field->setTitleColorizer($titleColorizer))
			->and($field->setDurationColorizer($durationColorizer))
			->and($field->setLocale($locale))
			->then
				->castToString($field)->isEqualTo($promptString . $colorizedTitle . ': ' . $colorizedDuration . '.')
				->mock($locale)
					->call('_')->withArguments('Running duration')->once()
					->call('_')->withArguments('unknown')->once()
					->call('_')->withArguments('%1$s: %2$s.')->once()
				->mock($titleColorizer)
					->call('colorize')->withArguments('Running duration')->once()
				->mock($durationColorizer)
					->call('colorize')->withArguments('unknown')->once()
			->assert
				->if($field->handleEvent(runner::runStart, $runner))
				->then
					->castToString($field)->isEqualTo($promptString . $colorizedTitle . ': ' . $colorizedDuration . '.')
					->mock($locale)
						->call('_')->withArguments('Running duration')->once()
						->call('_')->withArguments('unknown')->once()
						->call('_')->withArguments('%1$s: %2$s.')->once()
					->mock($titleColorizer)
						->call('colorize')->withArguments('Running duration')->once()
					->mock($durationColorizer)
						->call('colorize')->withArguments('unknown')->once()
			->assert
				->if($field->handleEvent(runner::runStop, $runner))
				->then
					->castToString($field)->isEqualTo($promptString . $colorizedTitle . ': ' . $colorizedDuration . '.')
					->mock($locale)
						->call('_')->withArguments('Running duration')->once()
						->call('__')->withArguments('%4.2f second', '%4.2f seconds', 1)->once()
						->call('_')->withArguments('%1$s: %2$s.')->once()
					->mock($titleColorizer)
						->call('colorize')->withArguments('Running duration')->once()
					->mock($durationColorizer)
						->call('colorize')->withArguments('1.00 second')->once()
			->assert
				->if($runner->getMockController()->getRunningDuration = $runningDuration = rand(2, PHP_INT_MAX))
				->and($field = new testedClass())
				->and($field->setPrompt($prompt))
				->and($field->setTitleColorizer($titleColorizer))
				->and($field->setDurationColorizer($durationColorizer))
				->and($field->setLocale($locale))
				->and($field->handleEvent(runner::runStart, $runner))
				->then
					->castToString($field)->isEqualTo($promptString . $colorizedTitle . ': ' . $colorizedDuration . '.')
					->mock($locale)
						->call('_')->withArguments('Running duration')->once()
						->call('_')->withArguments('unknown')->once()
						->call('_')->withArguments('%1$s: %2$s.')->once()
					->mock($titleColorizer)
						->call('colorize')->withArguments('Running duration')->once()
					->mock($durationColorizer)
						->call('colorize')->withArguments('unknown')->once()
			->assert
				->if($field->handleEvent(runner::runStop, $runner))
				->then
					->castToString($field)->isEqualTo($promptString . $colorizedTitle . ': ' . $colorizedDuration . '.')
					->mock($locale)
						->call('_')->withArguments('Running duration')->once()
						->call('__')->withArguments('%4.2f second', '%4.2f seconds', $runningDuration)->once()
						->call('_')->withArguments('%1$s: %2$s.')->once()
					->mock($titleColorizer)
						->call('colorize')->withArguments('Running duration')->once()
					->mock($durationColorizer)
						->call('colorize')->withArguments(sprintf('%4.2f', $runningDuration) . ' seconds')->once()
		;
	}
}
