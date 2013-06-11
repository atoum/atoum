<?php

namespace mageekguy\atoum\tests\units\mock\controller;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\controller,
	mageekguy\atoum\mock\controller\linker as testedClass
;

require __DIR__ . '../../../../runner.php';

class linker extends atoum\test
{
	public function testLink()
	{
		$this
			->given($linker = new testedClass())
			->and(controller::setLinker($linker))
			->then
				->if($mock = new \mock\foo())
				->and($controller = new \mock\mageekguy\atoum\mock\controller())
				->then
					->object($linker->link($controller, $mock))->isIdenticalTo($linker)
					->mock($controller)->call('control')->withArguments($mock)->once()
					->object($linker->getController($mock))
						->isIdenticalTo($controller)
						->isIdenticalTo($mock->getMockController())
					->object($linker->getMock($controller))
						->isIdenticalTo($mock)
		;
	}

	public function testGetController()
	{
		$this
			->given($linker = new testedClass())
			->and(controller::setLinker($linker))
			->then
				->if($mock = new \mock\foo())
				->then
					->object($linker->getController($mock))->isIdenticalTo($mock->getMockController())
				->if($otherMock = new \mock\foo())
				->then
					->object($linker->getController($mock))->isIdenticalTo($mock->getMockController())
					->object($linker->getController($otherMock))->isIdenticalTo($otherMock->getMockController())
		;
	}

	public function testGetMock()
	{
		$this
			->given($linker = new testedClass())
			->and(controller::setLinker($linker))
			->and($mock = new \mock\foo())
			->then
				->object($linker->getMock($mock->getMockController()))->isIdenticalTo($mock)
			->if($otherMock = new \mock\foo())
			->then
				->object($linker->getMock($mock->getMockController()))->isIdenticalTo($mock)
				->object($linker->getMock($otherMock->getMockController()))->isIdenticalTo($otherMock)
		;
	}

	public function testUnlink()
	{
		$this
			->given($linker = new testedClass())
			->and(controller::setLinker($linker))
			->then
				->if($linker->link($controller = new \mock\mageekguy\atoum\mock\controller(), $mock = new \mock\foo()))
				->then
					->object($linker->unlink($controller))->isIdenticalTo($linker)
					->variable($linker->getMock($controller))->isNull()
					->variable($linker->getController($mock))->isNull()
					->object($mock->getMockController())->isNotIdenticalTo($controller)
				->if($linker->link($controller = new \mock\mageekguy\atoum\mock\controller(), $mock))
				->and($linker->link($otherController = new controller(), $otherMock = new \mock\foo()))
				->then
					->object($linker->unlink($controller))->isIdenticalTo($linker)
					->object($linker->unlink($controller))->isIdenticalTo($linker)
					->variable($linker->getMock($controller))->isNull()
					->variable($linker->getController($mock))->isNull()
					->object($mock->getMockController())->isNotIdenticalTo($controller)
					->object($linker->getMock($otherController))->isIdenticalTo($otherMock)
					->variable($linker->getController($otherMock))->isIdenticalTo($otherController)
		;
	}

	public function testReset()
	{
		$this
			->given($linker = new testedClass())
			->and(controller::setLinker($linker))
			->then
				->if($linker->link($controller = new \mock\mageekguy\atoum\mock\controller(), $mock = new \mock\foo()))
				->then
					->object($linker->reset())->isIdenticalTo($linker)
					->variable($linker->getController($mock))->isNull()
					->variable($linker->getMock($controller))->isNull()
		;
	}
}
