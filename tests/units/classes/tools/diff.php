<?php

namespace mageekguy\atoum\tests\units\tools;

use
	atoum,
	atoum\tools
;

require_once __DIR__ . '/../../runner.php';

class diff extends atoum
{
	public function test__construct()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->variable($this->testedInstance->getExpected())->isNull()
				->variable($this->testedInstance->getActual())->isNull()
				->object($this->testedInstance->getDecorator())->isEqualTo(new tools\diff\decorator())

			->if($this->newTestedInstance($reference = uniqid()))
			->then
				->string($this->testedInstance->getExpected())->isEqualTo($reference)
				->variable($this->testedInstance->getActual())->isNull()
				->object($this->testedInstance->getDecorator())->isEqualTo(new tools\diff\decorator())

			->if($this->newTestedInstance('', $data = uniqid()))
			->then
				->string($this->testedInstance->getExpected())->isEmpty()
				->string($this->testedInstance->getActual())->isEqualTo($data)
				->object($this->testedInstance->getDecorator())->isEqualTo(new tools\diff\decorator())

			->if($this->newTestedInstance($reference = uniqid(), $data = uniqid()))
			->then
				->string($this->testedInstance->getExpected())->isEqualTo($reference)
				->string($this->testedInstance->getActual())->isEqualTo($data)
				->object($this->testedInstance->getDecorator())->isEqualTo(new tools\diff\decorator())
		;
	}

	public function test__toString()
	{
		$this
			->given(
				$this->newTestedInstance,
				$this->testedInstance->setDecorator($decorator = new \mock\atoum\tools\diff\decorator())
			)

			->if($this->calling($decorator)->decorate = uniqid())
			->then
				->castToString($this->testedInstance)->isEqualTo($decorator->decorate($this->testedInstance))
		;
	}

	public function test__invoke()
	{
		$this
			->if($diff = $this->newTestedInstance)
			->then
				->castToString($diff())->isEmpty()
				->object($diff())
					->isIdenticalTo($diff)
					->toString
						->isEmpty()
				->object($diff($expected = uniqid()))
					->isIdenticalTo($diff)
					->toString
						->isNotEmpty()
				->object($diff($expected = uniqid(), $actual = uniqid()))
					->isIdenticalTo($diff)
					->toString
						->isNotEmpty()
		;
	}

	public function testSetDecorator()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setDecorator($decorator = new tools\diff\decorator()))->isTestedInstance
				->object($this->testedInstance->getDecorator())->isIdenticalTo($decorator)
				->object($this->testedInstance->setDecorator())->isTestedInstance
				->object($this->testedInstance->getDecorator())
					->isNotIdenticalTo($decorator)
					->isEqualTo(new tools\diff\decorator())
		;
	}

	public function testSetExpected()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setExpected($reference = uniqid()))->isIdenticalTo($this->testedInstance)
				->string($this->testedInstance->getExpected())->isEqualTo($reference)
		;
	}

	public function testSetActual()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->object($this->testedInstance->setActual($data = uniqid()))->isIdenticalTo($this->testedInstance)
				->string($this->testedInstance->getActual())->isEqualTo($data)
		;
	}

	public function testMake()
	{
		$this
			->if($this->newTestedInstance)
			->then
				->array($this->testedInstance->make())->isEqualTo(array(''))
				->array($this->testedInstance->setActual($data = rand(0, 9))->make())->isEqualTo(array(
						array(
							'-' => array(''),
							'+' => array($data)
						)
					)
				)
				->array($this->testedInstance->setActual($data = uniqid())->make())->isEqualTo(array(
						array(
							'-' => array(''),
							'+' => array($data)
						)
					)
				)
				->array($this->testedInstance->setExpected($data)->make())->isEqualTo(array(
						$data
					)
				)
				->array($this->testedInstance->setExpected('')->setActual(($firstLine = uniqid()). PHP_EOL . ($secondLine = uniqid()))->make())->isEqualTo(array(
						array(
							'-' => array(''),
							'+' => array(
								$firstLine,
								$secondLine
							)
						)
					)
				)
		;
	}
}
