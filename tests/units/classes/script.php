<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mock\mageekguy\atoum as mock
;

require_once __DIR__ . '/../runner.php';

class script extends atoum\test
{
	public function testClassConstants()
	{
		$this->assert
			->string(atoum\script::padding)->isEqualTo('   ')
		;
	}

	public function test__construct()
	{
		$this->mock('mageekguy\atoum\script');


		$this->assert
			->if($script = new mock\script($name = uniqid()))
			->then
				->string($script->getName())->isEqualTo($name)
				->object($script->getLocale())->isEqualTo(new atoum\locale())
				->object($script->getAdapter())->isEqualTo(new atoum\adapter())
				->object($script->getArgumentsParser())->isEqualTo(new atoum\script\arguments\parser())
				->object($script->getOutputWriter())->isEqualTo(new atoum\writers\std\out())
				->object($script->getErrorWriter())->isEqualTo(new atoum\writers\std\err())
				->array($script->getHelp())->isEmpty()
			->if($factory = new atoum\factory())
			->and($factory->import('mageekguy\atoum'))
			->and($factory->import('mageekguy\atoum\writers\std'))
			->and($factory->import('mageekguy\atoum\script\arguments'))
			->and($factory->returnWhenBuild('atoum\locale', $locale = new atoum\locale()))
			->and($factory->returnWhenBuild('atoum\adapter', $adapter = new atoum\adapter()))
			->and($factory->returnWhenBuild('arguments\parser', $argumentsParser = new atoum\script\arguments\parser()))
			->and($factory->returnWhenBuild('std\out', $stdOut = new atoum\writers\std\out()))
			->and($factory->returnWhenBuild('std\err', $stdErr = new atoum\writers\std\err()))
			->and($script = new mock\script($name = uniqid(), $factory))
			->then
				->string($script->getName())->isEqualTo($name)
				->object($script->getLocale())->isIdenticalTo($locale)
				->object($script->getAdapter())->isIdenticalTo($adapter)
				->object($script->getArgumentsParser())->isIdenticalTo($argumentsParser)
				->object($script->getOutputWriter())->isIdenticalTo($stdOut)
				->object($script->getErrorWriter())->isIdenticalTo($stdErr)
				->array($script->getHelp())->isEmpty()
			->if($adapter = new atoum\test\adapter())
			->and($adapter->php_sapi_name = uniqid())
			->and($factory->returnWhenBuild('atoum\adapter', $adapter))
			->then
				->exception(function() use ($factory, & $name) {
						new mock\script($name = uniqid(), $factory);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('\'' . $name . '\' must be used in CLI only')
		;
	}

	public function testSetAdapter()
	{
		$this->mock('mageekguy\atoum\script');

		$script = new mock\script($name = uniqid());

		$this->assert
			->object($script->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($script)
			->object($script->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetLocale()
	{
		$this->mock('mageekguy\atoum\script');

		$script = new mock\script($name = uniqid());

		$this->assert
			->object($script->setLocale($locale = new atoum\locale()))->isIdenticalTo($script)
			->object($script->getLOcale())->isIdenticalTo($locale)
		;
	}

	public function testSetArgumentParser()
	{
		$this->mock('mageekguy\atoum\script');

		$script = new mock\script($name = uniqid());

		$this->assert
			->object($script->setArgumentsParser($argumentsParser = new atoum\script\arguments\parser()))->isIdenticalTo($script)
			->object($script->getArgumentsParser())->isIdenticalTo($argumentsParser)
		;
	}

	public function testSetOutputWriter()
	{
		$this->mock('mageekguy\atoum\script');

		$script = new mock\script($name = uniqid());

		$this->assert
			->object($script->setOutputWriter($outputWriter = new atoum\writers\std\out()))->isIdenticalTo($script)
			->object($script->getOutputWriter())->isIdenticalTo($outputWriter)
		;
	}

	public function testSetErrorWriter()
	{
		$this->mock('mageekguy\atoum\script');

		$script = new mock\script($name = uniqid());

		$this->assert
			->object($script->setErrorWriter($outputWriter = new atoum\writers\std\out()))->isIdenticalTo($script)
			->object($script->getErrorWriter())->isIdenticalTo($outputWriter)
		;
	}

	public function testAddArgumentHandler()
	{
		$this
			->mock('mageekguy\atoum\script')
			->mock('mageekguy\atoum\script\arguments\parser')
		;

		$argumentsParser = new mock\script\arguments\parser();
		$argumentsParser->getMockController()->addHandler = function() {};

		$script = new mock\script($name = uniqid());
		$script->setArgumentsParser($argumentsParser);

		$this->assert
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

	public function testHelp()
	{
		$this
			->mock('mageekguy\atoum\script')
			->mock('mageekguy\atoum\locale')
			->mock('mageekguy\atoum\script\arguments\parser')
		;

		$argumentsParser = new mock\script\arguments\parser();
		$argumentsParser->getMockController()->addHandler = function() {};

		$locale = new mock\locale();
		$locale->getMockController()->_ = function($string) { return $string; };

		$script = new mock\script($name = uniqid());
		$script
			->setArgumentsParser($argumentsParser)
			->setLocale($locale)
		;

		$script->getMockController()->writeMessage = $script;
		$script->getMockController()->writeLabels = $script;

		$this->assert
			->object($script->help())->isIdenticalTo($script)
			->mock($script)->call('writeMessage')->never()
			->mock($script)->call('writeLabels')->never()
		;

		$script->addArgumentHandler(function() {}, array('-c', '--c'), $valuesC = '<argumentC>', $helpC = 'help of C argument');

		$this->assert
			->object($script->help())->isIdenticalTo($script)
			->mock($locale)->call('_')->withArguments('Usage: %s [options]')->once()
			->mock($script)->call('writeMessage')->withArguments('Usage: ' . $script->getName() . ' [options]' . PHP_EOL)->once()
			->mock($script)->call('writeLabels')->withArguments(array('-c <argumentC>, --c <argumentC>' => $helpC), 1)->once()
		;
	}

	public function testRun()
	{
		$this
			->mock('mageekguy\atoum\script')
			->mock('mageekguy\atoum\script\arguments\parser')
			->assert
				->if($argumentsParser = new mock\script\arguments\parser())
				->and($argumentsParser->getMockController()->addHandler = function() {})
				->and($factory = new atoum\factory())
				->and($factory->import('mageekguy\atoum'))
				->and($factory->import('mageekguy\atoum\script\arguments'))
				->and($factory->returnWhenBuild('arguments\parser', $argumentsParser))
				->and($factory->returnWhenBuild('atoum\adapter', $adapter = new atoum\test\adapter()))
				->and($script = new mock\script(uniqid(), $factory))
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
		if (defined('STDIN') === false)
		{
			define('STDIN', rand(1, PHP_INT_MAX));
		}

		$this
			->mock('mageekguy\atoum\script')
			->mock('mageekguy\atoum\writers\std\out')
			->assert
				->if($stdOut = new mock\writers\std\out())
				->and($stdOut->getMockCOntroller()->write = function() {})
				->and($adapter = new atoum\test\adapter())
				->and($adapter->fgets = $input = uniqid())
				->and($factory = new atoum\factory())
				->and($factory->import('mageekguy\atoum'))
				->and($factory->returnWhenBuild('atoum\adapter', $adapter))
				->and($factory->returnWhenBuild('atoum\writers\std\out', $stdOut))
				->and($script = new mock\script(uniqid(), $factory))
				->then
					->string($script->prompt($message = uniqid()))->isEqualTo($input)
					->mock($stdOut)->call('write')->withArguments($message)->once()
					->adapter($adapter)->call('fgets')->withArguments(STDIN)->once()
			->assert
					->string($script->prompt(($message = ' ' . $message) . "\t\n"))->isEqualTo($input)
					->mock($stdOut)->call('write')->withArguments($message)->once()
					->adapter($adapter)->call('fgets')->withArguments(STDIN)->once()
			->assert
				->if($adapter->fgets = ' ' . ($input = uniqid()) . "\t")
				->then
					->string($script->prompt($message = uniqid()))->isEqualTo($input)
					->mock($stdOut)->call('write')->withArguments($message)->once()
					->adapter($adapter)->call('fgets')->withArguments(STDIN)->once()
		;
	}

	public function testWriteMessage()
	{
		$this
			->mock('mageekguy\atoum\script')
			->mock('mageekguy\atoum\writers\std\out')
		;

		$stdOut = new mock\writers\std\out();
		$stdOut->getMockCOntroller()->write = function() {};

		$script = new mock\script(uniqid());
		$script->setOutputWriter($stdOut);

		$this->assert
			->object($script->writeMessage($message = uniqid()))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments($message . PHP_EOL)->once()
		;

		$this->assert
			->object($script->writeMessage(($message = uniqid()) . PHP_EOL))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments($message . PHP_EOL)->once()
		;

		$this->assert
			->object($script->writeMessage(($message = uniqid()) . ' ' . PHP_EOL))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments($message . PHP_EOL)->once()
		;

		$this->assert
			->object($script->writeMessage(($message = PHP_EOL . $message) . ' ' . PHP_EOL))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments($message . PHP_EOL)->once()
		;
	}

	public function testWriteError()
	{
		$this
			->mock('mageekguy\atoum\script')
			->mock('mageekguy\atoum\locale')
			->mock('mageekguy\atoum\writers\std\err')
		;

		$script = new mock\script(uniqid());

		$stderr = new mock\writers\std\err();
		$stderr->getMockCOntroller()->write = function() {};

		$script->setErrorWriter($stderr);

		$locale = new mock\locale();
		$script->setLocale($locale);

		$this->assert
			->object($script->writeError($message = uniqid()))->isIdenticalTo($script)
			->mock($stderr)->call('write')->withArguments('Error: ' . $message . PHP_EOL)->once()
			->mock($locale)->call('_')->withArguments('Error: %s')->once()
		;

		$this->assert
			->object($script->writeError(($message = uniqid()) . PHP_EOL))->isIdenticalTo($script)
			->mock($stderr)->call('write')->withArguments('Error: ' . $message . PHP_EOL)->once()
			->mock($locale)->call('_')->withArguments('Error: %s')->once()
		;

		$this->assert
			->object($script->writeError(($message = uniqid()) . ' ' . PHP_EOL))->isIdenticalTo($script)
			->mock($stderr)->call('write')->withArguments('Error: ' . $message . PHP_EOL)->once()
			->mock($locale)->call('_')->withArguments('Error: %s')->once()
		;

		$this->assert
			->object($script->writeError((' ' . $message = uniqid()) . ' ' . PHP_EOL))->isIdenticalTo($script)
			->mock($stderr)->call('write')->withArguments('Error: ' . $message . PHP_EOL)->once()
			->mock($locale)->call('_')->withArguments('Error: %s')->once()
		;
	}

	public function testWriteLabel()
	{
		$this
			->mock('mageekguy\atoum\script')
			->mock('mageekguy\atoum\writers\std\out')
		;

		$stdOut = new mock\writers\std\out();
		$stdOut->getMockCOntroller()->write = function() {};

		$script = new mock\script(uniqid());
		$script->setOutputWriter($stdOut);

		$this->assert
			->object($script->writeLabel($label = uniqid(), $message = uniqid()))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments($label . ': ' . $message . PHP_EOL)->once()
		;

		$this->assert
			->object($script->writeLabel($label, $message, 0))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments($label . ': ' . $message . PHP_EOL)->once()
		;

		$this->assert
			->object($script->writeLabel(($label = ' ' . $label) . PHP_EOL, ' ' . $message . ' ' . PHP_EOL))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments($label . ': ' . $message . PHP_EOL)->once()
		;

		$this->assert
			->object($script->writeLabel($label, $message, 0))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments($label . ': ' . $message . PHP_EOL)->once()
		;

		$this->assert
			->object($script->writeLabel($label = uniqid(), $message = uniqid(), 1))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments(atoum\script::padding . $label . ': ' . $message . PHP_EOL)->once()
		;

		$this->assert
			->object($script->writeLabel($label, $message, 2))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments(atoum\script::padding . atoum\script::padding . $label . ': ' . $message . PHP_EOL)->once()
		;
	}

	public function testWriteLabels()
	{
		$this
			->mock('mageekguy\atoum\script')
			->mock('mageekguy\atoum\writers\std\out')
		;

		$stdOut = new mock\writers\std\out();
		$stdOut->getMockCOntroller()->write = function() {};

		$script = new mock\script(uniqid());
		$script->setOutputWriter($stdOut);

		$this->assert
			->object($script->writeLabels(array($label = uniqid() => $message = uniqid())))->isIdenticalTo($script)
			->mock($stdOut)->call('write')->withArguments(atoum\script::padding . $label . ': ' . $message . PHP_EOL)->once()
			->object($script->writeLabels(array(
						$label1 = uniqid() => $message1 = uniqid(),
						$label2 = uniqid() => $message2 = uniqid(),
						$label3 = uniqid() => $message3 = uniqid()
					)
				)
			)
				->isIdenticalTo($script)
			->mock($stdOut)
				->call('write')->withArguments(atoum\script::padding . $label1 . ': ' . $message1 . PHP_EOL)->once()
				->call('write')->withArguments(atoum\script::padding . $label2 . ': ' . $message2 . PHP_EOL)->once()
				->call('write')->withArguments(atoum\script::padding . $label3 . ': ' . $message3 . PHP_EOL)->once()
			->object($script->writeLabels(array(
						$label1 = uniqid() => $message1 = uniqid(),
						$label2 = '  ' . uniqid() => $message2 = uniqid(),
						$label3 = uniqid() => $message3 = uniqid()
					)
				)
			)
				->isIdenticalTo($script)
			->mock($stdOut)
				->call('write')->withArguments(atoum\script::padding . '  ' . $label1 . ': ' . $message1 . PHP_EOL)->once()
				->call('write')->withArguments(atoum\script::padding .        $label2 . ': ' . $message2 . PHP_EOL)->once()
				->call('write')->withArguments(atoum\script::padding . '  ' . $label3 . ': ' . $message3 . PHP_EOL)->once()
			->object($script->writeLabels(array(
						$label1 = uniqid() => $message1 = uniqid(),
						$label2 = 'xx' . uniqid() => $message2 = uniqid(),
						$label3 = uniqid() => $message3 = uniqid()
					), 3
				)
			)
				->isIdenticalTo($script)
			->mock($stdOut)
				->call('write')->withArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding . '  ' . $label1 . ': ' . $message1 . PHP_EOL)->once()
				->call('write')->withArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding .        $label2 . ': ' . $message2 . PHP_EOL)->once()
				->call('write')->withArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding . '  ' . $label3 . ': ' . $message3 . PHP_EOL)->once()
			->object($script->writeLabels(array(
						$label1 = uniqid() => $message1 = uniqid(),
						$label2 = 'xx' . uniqid() => ($message21 = uniqid()) . PHP_EOL . ($message22 = uniqid()),
						$label3 = uniqid() => $message3 = uniqid()
					), 3
				)
			)
				->isIdenticalTo($script)
			->mock($stdOut)
				->call('write')->withArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding . '  ' . $label1 . ': ' . $message1 . PHP_EOL)->once()
				->call('write')->withArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding .        $label2 . ': ' . $message21 . PHP_EOL)->once()
				->call('write')->withArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding . '               ' . ': ' . $message22 . PHP_EOL)->once()
				->call('write')->withArguments(atoum\script::padding . atoum\script::padding . atoum\script::padding . '  ' . $label3 . ': ' . $message3 . PHP_EOL)->once()
		;
	}
}

?>
