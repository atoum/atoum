<?php

namespace mageekguy\atoum\tests\units\tools;

use
	mageekguy\atoum,
	mageekguy\atoum\tools
;

require_once __DIR__ . '/../../runner.php';

class diff extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($diff = new tools\diff())
			->then
				->string($diff->getReference())->isEmpty()
				->string($diff->getData())->isEmpty()
			->if($diff = new tools\diff($reference = uniqid()))
			->then
				->string($diff->getReference())->isEqualTo($reference)
				->string($diff->getData())->isEmpty()
			->if($diff = new tools\diff('', $data = uniqid()))
			->then
				->string($diff->getReference())->isEmpty()
				->string($diff->getData())->isEqualTo($data)
			->if($diff = new tools\diff($reference = uniqid(), $data = uniqid()))
			->then
				->string($diff->getReference())->isEqualTo($reference)
				->string($diff->getData())->isEqualTo($data)
		;
	}

	public function test__toString()
	{
		$this
			->if($diff = new tools\diff())
			->then
				->castToString($diff)->isEmpty()
			->if($diff->setData($data = uniqid()))
			->then
				->castToString($diff)->isEqualTo(
					'-Reference' . PHP_EOL .
					'+Data' . PHP_EOL .
					'@@ -1 +1 @@' . PHP_EOL .
					'+' . $data
				)
			->if($diff->setData(($data = uniqid()) . PHP_EOL . ($otherSecondString = uniqid())))
			->then
				->castToString($diff)->isEqualTo(
					'-Reference' . PHP_EOL .
					'+Data' . PHP_EOL .
					'@@ -1 +1,2 @@' . PHP_EOL .
					'+' . $data . PHP_EOL .
					'+' . $otherSecondString
				)
			->if($diff->setReference($reference = 'check this dokument.'))
			->and($diff->setData($data = 'check this document.'))
			->then
				->castToString($diff)->isEqualTo(
					'-Reference' . PHP_EOL .
					'+Data' . PHP_EOL .
					'@@ -1 +1 @@' . PHP_EOL .
					'-' . $reference . PHP_EOL .
					'+' . $data
				)
			->if($diff->setReference($reference = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 4 . PHP_EOL . 5 . PHP_EOL)))
			->and($diff->setData($data = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 6 . PHP_EOL . 5 . PHP_EOL)))
			->then
				->castToString($diff)->isEqualTo(
					'-Reference' . PHP_EOL .
					'+Data' . PHP_EOL .
					'@@ -4 +4 @@' . PHP_EOL .
					'-4'. PHP_EOL .
					'+6'
				)
			->if($diff->setReference($reference = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 4 . PHP_EOL . 5 . PHP_EOL)))
			->and($diff->setData($data = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 6 . PHP_EOL . 7 . PHP_EOL . 5 . PHP_EOL)))
			->then
				->castToString($diff)->isEqualTo(
					'-Reference' . PHP_EOL .
					'+Data' . PHP_EOL .
					'@@ -4 +4,2 @@' . PHP_EOL .
					'-4'. PHP_EOL .
					'+6' . PHP_EOL .
					'+7'
				)
		;
	}

	public function testSetReference()
	{
		$this
			->if($diff = new tools\diff())
			->then
				->object($diff->setReference($reference = uniqid()))->isIdenticalTo($diff)
				->string($diff->getReference())->isEqualTo($reference)
		;
	}

	public function testSetData()
	{
		$this
			->if($diff = new tools\diff())
			->then
				->object($diff->setData($data = uniqid()))->isIdenticalTo($diff)
				->string($diff->getData())->isEqualTo($data)
		;
	}

	public function testMake()
	{
		$this
			->if($diff = new tools\diff())
			->then
				->array($diff->make())->isEqualTo(array(
						''
					)
				)
				->array($diff->setData($data = rand(0, 9))->make())->isEqualTo(array(
						array(
							'-' => array(''),
							'+' => array($data)
						)
					)
				)
				->array($diff->setData($data = uniqid())->make())->isEqualTo(array(
						array(
							'-' => array(''),
							'+' => array($data)
						)
					)
				)
				->array($diff->setReference($data)->make())->isEqualTo(array(
						$data
					)
				)
				->array($diff->setReference('')->setData(($firstLine = uniqid()). PHP_EOL . ($secondLine = uniqid()))->make())->isEqualTo(array(
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
