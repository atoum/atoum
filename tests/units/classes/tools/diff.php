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
		$diff = new tools\diff();

		$this->assert
			->string($diff->getReference())->isEmpty()
			->string($diff->getData())->isEmpty()
		;

		$diff = new tools\diff($reference = uniqid());

		$this->assert
			->string($diff->getReference())->isEqualTo($reference)
			->string($diff->getData())->isEmpty()
		;

		$diff = new tools\diff('', $data = uniqid());

		$this->assert
			->string($diff->getReference())->isEmpty()
			->string($diff->getData())->isEqualTo($data)
		;

		$diff = new tools\diff($reference = uniqid(), $data = uniqid());

		$this->assert
			->string($diff->getReference())->isEqualTo($reference)
			->string($diff->getData())->isEqualTo($data)
		;
	}

	public function test__toString()
	{
		$diff = new tools\diff();

		$this->assert
			->castToString($diff)->isEmpty()
		;

		$diff->setData($data = uniqid());

		$this->assert
			->castToString($diff)->isEqualTo(
				'-Reference' . PHP_EOL .
				'+Data' . PHP_EOL .
				'@@ -1 +1 @@' . PHP_EOL .
				'+' . $data
			)
		;

		$diff->setData(($data = uniqid()) . PHP_EOL . ($otherSecondString = uniqid()));

		$this->assert
			->castToString($diff)->isEqualTo(
				'-Reference' . PHP_EOL .
				'+Data' . PHP_EOL .
				'@@ -1 +1,2 @@' . PHP_EOL .
				'+' . $data . PHP_EOL .
				'+' . $otherSecondString
			)
		;

		$diff
			->setReference($reference = 'check this dokument.')
			->setData($data = 'check this document.')
		;

		$this->assert
			->castToString($diff)->isEqualTo(
				'-Reference' . PHP_EOL .
				'+Data' . PHP_EOL .
				'@@ -1 +1 @@' . PHP_EOL .
				'-' . $reference . PHP_EOL .
				'+' . $data
			)
		;

		$diff
			->setReference($reference = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 4 . PHP_EOL . 5 . PHP_EOL))
			->setData($data = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 6 . PHP_EOL . 5 . PHP_EOL))
		;

		$this->assert
			->castToString($diff)->isEqualTo(
				'-Reference' . PHP_EOL .
				'+Data' . PHP_EOL .
				'@@ -4 +4 @@' . PHP_EOL .
				'-4'. PHP_EOL .
				'+6'
			)
		;

		$diff
			->setReference($reference = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 4 . PHP_EOL . 5 . PHP_EOL))
			->setData($data = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 6 . PHP_EOL . 7 . PHP_EOL . 5 . PHP_EOL))
		;

		$this->assert
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
		$diff = new tools\diff();

		$this->assert
			->object($diff->setReference($reference = uniqid()))->isIdenticalTo($diff)
			->string($diff->getReference())->isEqualTo($reference)
		;
	}

	public function testSetData()
	{
		$diff = new tools\diff();

		$this->assert
			->object($diff->setData($data = uniqid()))->isIdenticalTo($diff)
			->string($diff->getData())->isEqualTo($data)
		;
	}

	public function testMake()
	{
		$diff = new tools\diff();

		$this->assert
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
