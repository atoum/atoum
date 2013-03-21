<?php

namespace mageekguy\atoum\tests\units\script;

use
	mageekguy\atoum,
	mock\mageekguy\atoum as mock,
	mock\mageekguy\atoum\script\prompt as testedClass
;

class prompt extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($prompt = new testedClass())
			->then
				->object($prompt->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
				->object($prompt->getOutputWriter())->isInstanceOf('mageekguy\atoum\writers\std\out')
			->if($prompt = new testedClass($adapter = new atoum\adapter()))
			->then
				->object($prompt->getAdapter())->isIdenticalTo($adapter)
				->object($prompt->getOutputWriter())->isInstanceOf('mageekguy\atoum\writers\std\out')
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($prompt = new testedClass())
			->then
				->object($prompt->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($prompt)
				->object($prompt->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetOutputWriter()
	{
		$this
			->if($prompt = new testedClass())
			->then
				->object($prompt->setOutputWriter($outputWriter = new atoum\writers\std\out()))->isIdenticalTo($prompt)
				->object($prompt->getOutputWriter())->isIdenticalTo($outputWriter)
		;
	}

	public function testGet()
	{
		if (defined('STDIN') === false)
		{
			define('STDIN', rand(1, PHP_INT_MAX));
		}

		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fgets = $input = uniqid())
			->and($prompt = new testedClass($adapter))
			->and($stdOut = new mock\writers\std\out())
			->and($stdOut->getMockController()->write = function() {})
			->and($prompt->setOutputWriter($stdOut))
			->then
				->string($prompt->get($message = uniqid()))->isEqualTo($input)
				->mock($stdOut)->call('write')->withIdenticalArguments($message)->once()
				->adapter($adapter)->call('fgets')->withArguments(STDIN)->once()
				->string($prompt->get(($message = ' ' . $message) . "\t\n"))->isEqualTo($input)
				->mock($stdOut)->call('write')->withIdenticalArguments($message)->once()
				->adapter($adapter)->call('fgets')->withArguments(STDIN)->exactly(2)
			->if($adapter->fgets = ' ' . ($input = uniqid()) . "\t")
			->then
				->string($prompt->get($message = uniqid()))->isEqualTo($input)
				->mock($stdOut)->call('write')->withIdenticalArguments($message)->once()
				->adapter($adapter)->call('fgets')->withArguments(STDIN)->exactly(3)
		;
	}

	public function testSelect()
	{
		if (defined('STDIN') === false)
		{
			define('STDIN', rand(1, PHP_INT_MAX));
		}

		$generateRandomChoice = function()
		{
			$choices = array();

			$length = rand(3, 10);

			for($i = 0; $i < $length; $i++)
			{
				$choices[] = uniqid();
			}

			return $choices;
		};

		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->fgets = uniqid())
			->and($prompt = new testedClass($adapter))
			->and($stdOut = new mock\writers\std\out())
			->and($stdOut->getMockController()->write = function() {})
			->and($prompt->setOutputWriter($stdOut))
				->exception(
					function() use($prompt)
					{
						$prompt->select(uniqid(), array());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('You must specify at least one choice to use \'mageekguy\\atoum\\script\prompt::select\'')


			->if($choices = $generateRandomChoice())
			->and($adapter->fgets = $input = $choices[array_rand($choices)])
			->and($stdOut->getMockController()->resetCalls())
			->then
				->string($prompt->select($message = uniqid(), $choices))
					->isEqualTo($input)
				->mock($stdOut)->call('write')
					->withIdenticalArguments($message . ' (' . implode($choices, '/') . ')')->once()
		;

		unset($adapter->fgets);

		$this
			->if($choices = $generateRandomChoice())
			->and($adapter->fgets[1] = uniqid())
			->and($adapter->fgets[2] = $input = $choices[array_rand($choices)])
			->and($stdOut->getMockController()->resetCalls())
			->then
				->string($prompt->select($message = uniqid(), $choices))
					->isEqualTo($input)
				->mock($stdOut)->call('write')
					->withIdenticalArguments($message . ' (' . implode($choices, '/') . ')')->twice()


			->if($choices = $generateRandomChoice())
			->and($adapter->fgets = '')
			->and($stdOut->getMockController()->resetCalls())
			->then
				->string($prompt->select($message = uniqid(), $choices, $default = $choices[array_rand($choices)]))
					->isEqualTo($default)
				->mock($stdOut)->call('write')
					->withIdenticalArguments($message . ' (' . implode($choices, '/') . ') [' . $default . ']')->once()

			->if($choices = $generateRandomChoice())
			->and($adapter->fgets = $input = $choices[array_rand($choices)])
			->and($stdOut->getMockController()->resetCalls())
			->then
				->string($prompt->select($message = uniqid(), $choices, $default = $choices[array_rand($choices)]))
					->isEqualTo($input)
				->mock($stdOut)->call('write')
					->withIdenticalArguments($message . ' (' . implode($choices, '/') . ') [' . $default . ']')->once()
		;

		unset($adapter->fgets);

		$this
			->if($choices = $generateRandomChoice())
			->and($adapter->fgets[1] = uniqid())
			->and($adapter->fgets[2] = $input = $choices[array_rand($choices)])
			->and($stdOut->getMockController()->resetCalls())
			->then
				->string($prompt->select($message = uniqid(), $choices, $default = $choices[array_rand($choices)]))
					->isEqualTo($input)
				->mock($stdOut)->call('write')
					->withIdenticalArguments($message . ' (' . implode($choices, '/') . ') [' . $default . ']')->twice()
		;
	}
}
