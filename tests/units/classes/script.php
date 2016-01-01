<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\writer,
	mageekguy\atoum\writers,
	mageekguy\atoum\script\prompt,
	mock\mageekguy\atoum as mock
;

require_once __DIR__ . '/../runner.php';

class script extends atoum\test
{
	public function testClassConstants()
	{
		$this->string(atoum\script::padding)->isEqualTo('   ');
	}

	public function test__construct()
	{
		$this
			->given(
				$labelColorizer = new atoum\cli\colorizer('0;32'),
				$labelColorizer->setPattern('/(^[^:]+: )/'),
				$argumentColorizer = new atoum\cli\colorizer('0;32'),
				$argumentColorizer->setPattern('/((?:^| )[-+]+[-a-z]+)/'),
				$valueColorizer = new atoum\cli\colorizer('0;34'),
				$valueColorizer->setPattern('/(<[^>]+>(?:\.\.\.)?)/'),

				$defaultOutputWriter = new writers\std\out(),

				$defaultInfoWriter = new writers\std\out(),
				$defaultInfoWriter
					->addDecorator(new writer\decorators\rtrim())
					->addDecorator(new writer\decorators\eol())
					->addDecorator(new atoum\cli\clear()),

				$defaultWarningWriter = new writers\std\err(),
				$defaultWarningWriter
					->addDecorator(new writer\decorators\trim())
					->addDecorator(new writer\decorators\prompt('Warning: '))
					->addDecorator(new writer\decorators\eol())
					->addDecorator(new atoum\cli\clear()),

				$defaultErrorWriter = new writers\std\err(),
				$defaultErrorWriter
					->addDecorator(new writer\decorators\trim())
					->addDecorator(new writer\decorators\prompt('Error: '))
					->addDecorator(new writer\decorators\eol())
					->addDecorator(new atoum\cli\clear()),

				$defaultHelpWriter = new writers\std\out(),
				$defaultHelpWriter
					->addDecorator($labelColorizer)
					->addDecorator($valueColorizer)
					->addDecorator($argumentColorizer)
					->addDecorator(new writer\decorators\rtrim())
					->addDecorator(new writer\decorators\eol())
					->addDecorator(new atoum\cli\clear())
			)
			->if($script = new mock\script($name = uniqid()))
			->then
				->string($script->getName())->isEqualTo($name)
				->object($script->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($script->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($script->getArgumentsParser())->isInstanceOf('mageekguy\atoum\script\arguments\parser')
				->object($script->getOutputWriter())->isEqualTo($defaultOutputWriter)
				->object($script->getInfoWriter())->isEqualTo($defaultInfoWriter)
				->object($script->getErrorWriter())->isEqualTo($defaultErrorWriter)
				->object($script->getWarningWriter())->isEqualTo($defaultWarningWriter)
				->object($script->getHelpWriter())->isEqualTo($defaultHelpWriter)
				->array($script->getHelp())->isEmpty()
				->object($script->getCli())->isEqualTo(new atoum\cli())
				->integer($script->getVerbosityLevel())->isZero()
			->and($script = new mock\script($name = uniqid(), $adapter = new atoum\adapter()))
			->then
				->string($script->getName())->isEqualTo($name)
				->object($script->getAdapter())->isIdenticalTo($adapter)
				->object($script->getLocale())->isInstanceOf('mageekguy\atoum\locale')
				->object($script->getArgumentsParser())->isInstanceOf('mageekguy\atoum\script\arguments\parser')
				->object($script->getOutputWriter())->isEqualTo($defaultOutputWriter)
				->object($script->getInfoWriter())->isEqualTo($defaultInfoWriter)
				->object($script->getErrorWriter())->isEqualTo($defaultErrorWriter)
				->object($script->getWarningWriter())->isEqualTo($defaultWarningWriter)
				->object($script->getHelpWriter())->isEqualTo($defaultHelpWriter)
				->array($script->getHelp())->isEmpty()
				->object($script->getCli())->isEqualTo(new atoum\cli())
				->integer($script->getVerbosityLevel())->isZero()
			->if($adapter = new atoum\test\adapter())
			->and($adapter->php_sapi_name = uniqid())
			->then
				->exception(function() use ($adapter, & $name) {
						new mock\script($name = uniqid(), $adapter);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('\'' . $name . '\' must be used in CLI only')
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($script = new mock\script($name = uniqid()))
			->then
				->object($script->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($script)
				->object($script->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetLocale()
	{
		$this
			->if($script = new mock\script($name = uniqid()))
			->then
				->object($script->setLocale($locale = new atoum\locale()))->isIdenticalTo($script)
				->object($script->getLocale())->isIdenticalTo($locale)
		;
	}

	public function testSetCli()
	{
		$this
			->if($script = new mock\script(uniqid()))
			->then
				->object($script->setCli($cli = new atoum\cli()))->isIdenticalTo($script)
				->object($script->getCli())->isIdenticalTo($cli)
				->object($script->setCli())->isIdenticalTo($script)
				->object($script->getCli())
					->isNotIdenticalTo($cli)
					->isEqualTo(new atoum\cli())
		;
	}

	public function testSetArgumentParser()
	{
		$this
			->if($script = new mock\script($name = uniqid()))
			->then
				->object($script->setArgumentsParser($argumentsParser = new atoum\script\arguments\parser()))->isIdenticalTo($script)
				->object($script->getArgumentsParser())->isIdenticalTo($argumentsParser)
		;
	}

	public function testSetOutputWriter()
	{
		$this
			->if($script = new mock\script($name = uniqid()))
			->then
				->object($script->setOutputWriter($outputWriter = new writers\std\out()))->isIdenticalTo($script)
				->object($script->getOutputWriter())->isIdenticalTo($outputWriter)

			->given(
				$defaultOutputWriter = new writers\std\out()
			)
			->then
				->object($script->setOutputWriter())->isIdenticalTo($script)
				->object($script->getOutputWriter())
					->isNotIdenticalTo($outputWriter)
					->isEqualTo($defaultOutputWriter)
				->object($script->getOutputWriter()->getCli())->isIdenticalTo($script->getCli())
		;
	}

	public function testSetInfoWriter()
	{
		$this
			->if($script = new mock\script($name = uniqid()))
			->then
				->object($script->setInfoWriter($infoWriter = new writers\std\out()))->isIdenticalTo($script)
				->object($script->getInfoWriter())->isIdenticalTo($infoWriter)

			->given(
				$defaultInfoWriter = new writers\std\out(),
				$defaultInfoWriter
					->addDecorator(new writer\decorators\rtrim())
					->addDecorator(new writer\decorators\eol())
					->addDecorator(new atoum\cli\clear())
			)
				->object($script->setInfoWriter())->isIdenticalTo($script)
				->object($script->getInfoWriter())
					->isNotIdenticalTo($infoWriter)
					->isEqualTo($defaultInfoWriter)
				->object($script->getInfoWriter()->getCli())->isIdenticalTo($script->getCli())
		;
	}

	public function testSetWarningWriter()
	{
		$this
			->if($script = new mock\script($name = uniqid()))
			->then
				->object($script->setWarningWriter($warningWriter = new writers\std\out()))->isIdenticalTo($script)
				->object($script->getWarningWriter())->isIdenticalTo($warningWriter)

			->given(
				$defaultWarningWriter = new writers\std\err(),
				$defaultWarningWriter
					->addDecorator(new writer\decorators\trim())
					->addDecorator(new writer\decorators\prompt($script->getLocale()->_('Warning: ')))
					->addDecorator(new writer\decorators\eol())
					->addDecorator(new atoum\cli\clear())
			)
			->then
				->object($script->setWarningWriter())->isIdenticalTo($script)
				->object($script->getWarningWriter())
					->isNotIdenticalTo($warningWriter)
					->isEqualTo($defaultWarningWriter)
				->object($script->getWarningWriter()->getCli())->isIdenticalTo($script->getCli())
		;
	}

	public function testSetErrorWriter()
	{
		$this
			->if($script = new mock\script($name = uniqid()))
			->then
				->object($script->setErrorWriter($errorWriter = new writers\std\out()))->isIdenticalTo($script)
				->object($script->getErrorWriter())->isIdenticalTo($errorWriter)

			->given(
				$defaultErrorWriter = new writers\std\err(),
				$defaultErrorWriter
					->addDecorator(new writer\decorators\trim())
					->addDecorator(new writer\decorators\prompt($script->getLocale()->_('Error: ')))
					->addDecorator(new writer\decorators\eol())
					->addDecorator(new atoum\cli\clear())
			)
			->then
				->object($script->setErrorWriter())->isIdenticalTo($script)
				->object($script->getErrorWriter())
					->isNotIdenticalTo($errorWriter)
					->isEqualTo($defaultErrorWriter)
				->object($script->getErrorWriter()->getCli())->isIdenticalTo($script->getCli())
		;
	}

	public function testSetHelpWriter()
	{
		$this
			->if($script = new mock\script($name = uniqid()))
			->then
				->object($script->setHelpWriter($helpWriter = new writers\std\out()))->isIdenticalTo($script)
				->object($script->getHelpWriter())->isIdenticalTo($helpWriter)
			->given(
				$labelColorizer = new atoum\cli\colorizer('0;32'),
				$labelColorizer->setPattern('/(^[^:]+: )/'),
				$argumentColorizer = new atoum\cli\colorizer('0;32'),
				$argumentColorizer->setPattern('/((?:^| )[-+]+[-a-z]+)/'),
				$valueColorizer = new atoum\cli\colorizer('0;34'),
				$valueColorizer->setPattern('/(<[^>]+>(?:\.\.\.)?)/'),
				$defaultHelpWriter = new writers\std\out(),
				$defaultHelpWriter
					->addDecorator($labelColorizer)
					->addDecorator($valueColorizer)
					->addDecorator($argumentColorizer)
					->addDecorator(new writer\decorators\rtrim())
					->addDecorator(new writer\decorators\eol())
					->addDecorator(new atoum\cli\clear())
			)
			->then
				->object($script->setHelpWriter())->isIdenticalTo($script)
				->object($script->getHelpWriter())->isEqualTo($defaultHelpWriter)
		;
	}

	public function testSetPrompt()
	{
		$this
			->if($script = new mock\script(uniqid()))
			->then
				->object($script->setPrompt($prompt = new prompt()))->isIdenticalTo($script)
				->object($script->getPrompt())->isIdenticalTo($prompt)
				->object($prompt->getOutputWriter())->isIdenticalTo($script->getOutputWriter())

			->given(
				$defaultPrompt = new prompt(),
				$defaultPrompt->setOutputWriter($script->getOutputWriter())
			)
			->then
				->object($script->setPrompt())->isIdenticalTo($script)
				->object($script->getPrompt())
					->isNotIdenticalTo($prompt)
					->isEqualTo($defaultPrompt)
		;
	}

	public function testAddArgumentHandler()
	{
		$this
			->if($argumentsParser = new mock\script\arguments\parser())
			->and($this->calling($argumentsParser)->addHandler = function() {})
			->and($script = new mock\script($name = uniqid()))
			->and($script->setArgumentsParser($argumentsParser))
			->then
				->object($script->addArgumentHandler($handlerA = function() {}, $argumentsA = array('-a')))->isIdenticalTo($script)
				->mock($argumentsParser)->call('addHandler')->withArguments($handlerA, $argumentsA)->once()
				->array($script->getHelp())->isEmpty()
				->object($script->addArgumentHandler($handlerB = function() {}, $argumentsB = array('-b', '--b'), $valuesB = '<argumentB>'))->isIdenticalTo($script)
				->mock($argumentsParser)->call('addHandler')->withArguments($handlerB, $argumentsB)->once()
				->array($script->getHelp())->isEmpty()
				->object($script->addArgumentHandler($handlerC = function() {}, $argumentsC = array('-c', '--c'), $valuesC = '<argumentC>', $helpC = 'help of C argument'))->isIdenticalTo($script)
				->mock($argumentsParser)->call('addHandler')->withArguments($handlerC, $argumentsC)->once()
				->array($script->getHelp())->isEqualTo(array(array($argumentsC, $valuesC, $helpC)))
		;
	}

	public function testSetDefaultArgumentHandler()
	{
		$this
			->if($argumentsParser = new mock\script\arguments\parser())
			->and($this->calling($argumentsParser)->addHandler = function() {})
			->and($script = new mock\script($name = uniqid()))
			->and($script->setArgumentsParser($argumentsParser))
			->then
				->object($script->setDefaultArgumentHandler($defaultHandler = function($script, $argument) {}))->isIdenticalTo($script)
				->mock($argumentsParser)->call('setDefaultHandler')->withArguments($defaultHandler)->once()
				->array($script->getHelp())->isEmpty()
		;
	}

	public function testIncreaseVerbosityLevel()
	{
		$this
			->if($script = new mock\script(uniqid()))
			->then
				->object($script->increaseVerbosityLevel())->isIdenticalTo($script)
				->integer($script->getVerbosityLevel())->isEqualTo(1)
				->object($script->increaseVerbosityLevel())->isIdenticalTo($script)
				->integer($script->getVerbosityLevel())->isEqualTo(2)
				->object($script->increaseVerbosityLevel())->isIdenticalTo($script)
				->integer($script->getVerbosityLevel())->isEqualTo(3)
		;
	}

	public function testDecreaseVerbosityLevel()
	{
		$this
			->if($script = new mock\script(uniqid()))
			->then
				->object($script->DecreaseVerbosityLevel())->isIdenticalTo($script)
				->integer($script->getVerbosityLevel())->isZero()
			->if($script->increaseVerbosityLevel())
			->then
				->object($script->DecreaseVerbosityLevel())->isIdenticalTo($script)
				->integer($script->getVerbosityLevel())->isZero()
				->object($script->DecreaseVerbosityLevel())->isIdenticalTo($script)
				->integer($script->getVerbosityLevel())->isZero()
		;
	}

	public function testResetVerbosityLevel()
	{
		$this
			->if($script = new mock\script(uniqid()))
			->then
				->object($script->resetVerbosityLevel())->isIdenticalTo($script)
				->integer($script->getVerbosityLevel())->isZero()
			->if($script->increaseVerbosityLevel())
			->and($script->increaseVerbosityLevel())
			->then
				->object($script->resetVerbosityLevel())->isIdenticalTo($script)
				->integer($script->getVerbosityLevel())->isZero()
		;
	}

	public function testHelp()
	{
		$this
			->if($argumentsParser = new mock\script\arguments\parser())
			->and($this->calling($argumentsParser)->addHandler = function() {})
			->and($locale = new mock\locale())
			->and($this->calling($locale)->_ = function($string) { return vsprintf($string, array_slice(func_get_args(), 1)); })
			->and($helpWriter = new mock\writers\std\out())
			->and($this->calling($helpWriter)->write = function() {})
			->and($script = new mock\script($name = uniqid()))
			->and($script->setArgumentsParser($argumentsParser))
			->and($script->setLocale($locale))
			->and($script->setHelpWriter($helpWriter))
			->then
				->object($script->help())->isIdenticalTo($script)
				->mock($helpWriter)->call('write')->never()
			->if($script->addArgumentHandler(function() {}, array('-c', '--c'), $valuesC = '<argumentC>', $helpC = 'help of C argument'))
			->then
				->object($script->help())->isIdenticalTo($script)
				->mock($locale)->call('_')->withArguments('Usage: %s [options]')->once()
				->mock($helpWriter)
					->call('write')
						->withArguments('Usage: ' . $script->getName() . ' [options]')->once()
						->withArguments('Available options are:')->once()
						->withArguments('   -c <argumentC>, --c <argumentC>: help of C argument')->once()
		;
	}

	public function testRun()
	{
		$this
			->if($script = new mock\script(uniqid(), $adapter = new atoum\test\adapter()))
			->and($argumentsParser = new mock\script\arguments\parser())
			->and($this->calling($argumentsParser)->addHandler = function() {})
			->and($script->setArgumentsParser($argumentsParser))
			->then
				->object($script->run())->isIdenticalTo($script)
				->mock($argumentsParser)->call('parse')->withArguments($script, array())->once()
				->adapter($adapter)->call('ini_set')->withArguments('log_errors_max_len', 0)->once()
				->adapter($adapter)->call('ini_set')->withArguments('log_errors', 'Off')->once()
				->adapter($adapter)->call('ini_set')->withArguments('display_errors', 'stderr')->once()
		;
	}

	public function testPrompt()
	{
		$this
			->if($prompt = new mock\script\prompt())
			->and($this->calling($prompt)->ask = $answer = uniqid())
			->and($script = new mock\script(uniqid()))
			->and($script->setPrompt($prompt))
			->then
				->string($script->prompt($message = uniqid()))->isEqualTo($answer)
				->mock($prompt)->call('ask')->withIdenticalArguments($message)->once()
				->string($script->prompt(($message = ' ' . $message) . "\t\n"))->isEqualTo($answer)
				->mock($prompt)->call('ask')->withIdenticalArguments($message)->once()
			->if($this->calling($prompt)->ask = ' ' . ($answer = uniqid()) . "\t")
			->then
				->string($script->prompt($message = uniqid()))->isEqualTo($answer)
				->mock($prompt)->call('ask')->withIdenticalArguments($message)->once()
		;
	}

	public function testWriteMessage()
	{
		$this
			->if($outputWriter = new mock\writers\std\out())
			->and($this->calling($outputWriter)->write->doesNothing())
			->and($script = new mock\script(uniqid()))
			->and($script->setOutputWriter($outputWriter))
			->then
				->object($script->writeMessage($message = uniqid()))->isIdenticalTo($script)
				->mock($outputWriter)
					->call('write')
						->withArguments($message)
						->after($this->mock($outputWriter)->call('removeDecorators')->once())
							->once()
		;
	}

	public function testWriteInfo()
	{
		$this
			->if($infoWriter = new mock\writers\std\out())
			->and($this->calling($infoWriter)->write->doesNothing())
			->and($script = new mock\script(uniqid()))
			->and($script->setInfoWriter($infoWriter))
			->then
				->object($script->writeInfo($info = uniqid()))->isIdenticalTo($script)
				->mock($infoWriter)->call('write')->withArguments($info)->once()
		;
	}

	public function testWriteWarning()
	{
		$this
			->if($errorWriter = new mock\writers\std\err())
			->and($this->calling($errorWriter)->clear = $errorWriter)
			->and($this->calling($errorWriter)->write->doesNothing())
			->and($script = new mock\script(uniqid()))
			->and($script->setWarningWriter($errorWriter))
			->then
				->object($script->writeWarning($warning = uniqid()))->isIdenticalTo($script)
				->mock($errorWriter)->call('write')->withArguments($warning)->once()
		;
	}

	public function testWriteError()
	{
		$this
			->if($errorWriter = new mock\writers\std\err())
			->and($this->calling($errorWriter)->clear = $errorWriter)
			->and($this->calling($errorWriter)->write->doesNothing())
			->and($script = new mock\script(uniqid()))
			->and($script->setErrorWriter($errorWriter))
			->then
				->object($script->writeError($message = uniqid()))->isIdenticalTo($script)
				->mock($errorWriter)->call('write')->withIdenticalArguments($message)->once()
		;
	}

	public function testVerbose()
	{
		$this
			->if($script = new mock\script(uniqid()))
			->and($script->setInfoWriter($infoWriter = new mock\writers\std\out()))
			->and($this->calling($infoWriter)->write->doesNothing())
			->then
				->object($script->verbose($message = uniqid()))->isIdenticalTo($script)
				->mock($infoWriter)->call('write')->withIdenticalArguments($message . PHP_EOL)->never()
			->if($script->increaseVerbosityLevel())
			->then
				->object($script->verbose($message = uniqid()))->isIdenticalTo($script)
				->mock($infoWriter)->call('write')->withIdenticalArguments($message)->once()
				->object($script->verbose($message, 1))->isIdenticalTo($script)
				->mock($infoWriter)->call('write')->withIdenticalArguments($message)->twice()
				->object($script->verbose($message, rand(2, PHP_INT_MAX)))->isIdenticalTo($script)
				->mock($infoWriter)->call('write')->withIdenticalArguments($message)->twice()
				->object($script->verbose($message = uniqid(), 0))->isIdenticalTo($script)
				->mock($infoWriter)->call('write')->withIdenticalArguments($message)->never()
				->object($script->verbose($message, 1))->isIdenticalTo($script)
				->mock($infoWriter)->call('write')->withIdenticalArguments($message)->once()
		;
	}

	public function testClearMessage()
	{
		$this
			->if($script = new mock\script(uniqid()))
			->and($script->setOutputWriter($outputWriter = new mock\writers\std\out()))
			->and($this->calling($outputWriter)->clear->doesNothing())
			->then
				->object($script->clearMessage($message = uniqid()))->isIdenticalTo($script)
				->mock($outputWriter)->call('clear')->once()
		;
	}

	public function testWriteLabel()
	{
		$this
			->if($script = new mock\script(uniqid()))
			->and($script->setHelpWriter($helpWriter = new mock\writers\std\out()))
			->and($this->calling($helpWriter)->write->doesNothing())
			->then
				->object($script->writeLabel($label = uniqid(), $message = uniqid()))->isIdenticalTo($script)
				->mock($helpWriter)->call('write')->withIdenticalArguments($label . ': ' . $message)->once()
				->object($script->writeLabel($label, $message, 0))->isIdenticalTo($script)
				->mock($helpWriter)->call('write')->withIdenticalArguments($label . ': ' . $message)->exactly(2)
				->object($script->writeLabel(($label = ' ' . $label) . PHP_EOL, ' ' . $message . ' ' . PHP_EOL))->isIdenticalTo($script)
				->mock($helpWriter)->call('write')->withIdenticalArguments($label . ': ' . $message)->once()
				->object($script->writeLabel($label, $message, 0))->isIdenticalTo($script)
				->mock($helpWriter)->call('write')->withIdenticalArguments($label . ': ' . $message)->exactly(2)
				->object($script->writeLabel($label = uniqid(), $message = uniqid(), 1))->isIdenticalTo($script)
				->mock($helpWriter)->call('write')->withIdenticalArguments(atoum\script::padding . $label . ': ' . $message)->once()
				->object($script->writeLabel($label, $message, 2))->isIdenticalTo($script)
				->mock($helpWriter)->call('write')->withIdenticalArguments(atoum\script::padding . atoum\script::padding . $label . ': ' . $message)->once()
		;
	}

	public function testWriteLabels()
	{
		$this
			->if($script = new mock\script(uniqid()))
			->and($script->setHelpWriter($helpWriter = new mock\writers\std\out()))
			->and($this->calling($helpWriter)->write->doesNothing())
			->then
				->object($script->writeLabels(array($label = uniqid() => $message = uniqid())))->isIdenticalTo($script)
				->mock($helpWriter)->call('write')->withIdenticalArguments(atoum\script::padding . $label . ': ' . $message)->once()
				->object($script->writeLabels(
						array(
							$label1 = uniqid() => $message1 = uniqid(),
							$label2 = uniqid() => $message2 = uniqid(),
							$label3 = uniqid() => $message3 = uniqid()
						)
					)
				)
					->isIdenticalTo($script)
				->mock($helpWriter)
					->call('write')->withIdenticalArguments(atoum\script::padding . $label1 . ': ' . $message1)->once()
					->call('write')->withIdenticalArguments(atoum\script::padding . $label2 . ': ' . $message2)->once()
					->call('write')->withIdenticalArguments(atoum\script::padding . $label3 . ': ' . $message3)->once()
				->object($script->writeLabels(
						array(
							$label1 = uniqid() => $message1 = uniqid(),
							$label2 = '  ' . uniqid() => $message2 = uniqid(),
							$label3 = uniqid() => $message3 = uniqid()
						)
					)
				)
					->isIdenticalTo($script)
				->mock($helpWriter)
					->call('write')->withIdenticalArguments(atoum\script::padding . '  ' . $label1 . ': ' . $message1)->once()
					->call('write')->withIdenticalArguments(atoum\script::padding .        $label2 . ': ' . $message2)->once()
					->call('write')->withIdenticalArguments(atoum\script::padding . '  ' . $label3 . ': ' . $message3)->once()
				->object($script->writeLabels(array(
							$label1 = uniqid() => $message1 = uniqid(),
							$label2 = 'xx' . uniqid() => $message2 = uniqid(),
							$label3 = uniqid() => $message3 = uniqid()
						), 3
					)
				)
					->isIdenticalTo($script)
				->mock($helpWriter)
					->call('write')->withIdenticalArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding . '  ' . $label1 . ': ' . $message1)->once()
					->call('write')->withIdenticalArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding .        $label2 . ': ' . $message2)->once()
					->call('write')->withIdenticalArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding . '  ' . $label3 . ': ' . $message3)->once()
				->object($script->writeLabels(array(
							$label1 = uniqid() => $message1 = uniqid(),
							$label2 = 'xx' . uniqid() => ($message21 = uniqid()) . PHP_EOL . ($message22 = uniqid()),
							$label3 = uniqid() => $message3 = uniqid()
						), 3
					)
				)
					->isIdenticalTo($script)
				->mock($helpWriter)
					->call('write')->withIdenticalArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding . '  ' . $label1 . ': ' . $message1)->once()
					->call('write')->withIdenticalArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding .        $label2 . ': ' . $message21)->once()
					->call('write')->withIdenticalArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding . '               ' . ': ' . $message22)->once()
					->call('write')->withIdenticalArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding . '  ' . $label3 . ': ' . $message3)->once()
		;
	}

	public function testGetDirectory()
	{
		$this
			->given($script = new mock\script($name = uniqid()))
			->and($script->setAdapter($adapter = new atoum\test\adapter()))
			->and($adapter->is_dir = true)
			->and($adapter->dirname = $directory = uniqid())
			->then
				->string($script->getDirectory())->isEqualTo($directory . DIRECTORY_SEPARATOR)
			->if($adapter->dirname = $directory . DIRECTORY_SEPARATOR)
			->then
				->string($script->getDirectory())->isEqualTo($directory . DIRECTORY_SEPARATOR)
			->if($adapter->is_dir = false)
			->and($adapter->getcwd = $currentDirectory = uniqid())
			->then
				->string($script->getDirectory())->isEqualTo($currentDirectory . DIRECTORY_SEPARATOR)
			->and($adapter->getcwd = $currentDirectory . DIRECTORY_SEPARATOR)
			->then
				->string($script->getDirectory())->isEqualTo($currentDirectory . DIRECTORY_SEPARATOR)
		;
	}
}
