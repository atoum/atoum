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
			->string($diff->getExpected())->isEmpty()
			->string($diff->getActual())->isEmpty()
		;

		$diff = new tools\diff($reference = uniqid());

		$this->assert
			->string($diff->getExpected())->isEqualTo($reference)
			->string($diff->getActual())->isEmpty()
		;

		$diff = new tools\diff('', $data = uniqid());

		$this->assert
			->string($diff->getExpected())->isEmpty()
			->string($diff->getActual())->isEqualTo($data)
		;

		$diff = new tools\diff($reference = uniqid(), $data = uniqid());

		$this->assert
			->string($diff->getExpected())->isEqualTo($reference)
			->string($diff->getActual())->isEqualTo($data)
		;
	}

	public function test__toString()
	{
		$diff = new tools\diff();

		$this->assert
			->castToString($diff)->isEmpty()
		;

		$diff->setActual($data = uniqid());

		$this->assert
			->castToString($diff)->isEqualTo(
				'-Expected' . PHP_EOL .
				'+Actual' . PHP_EOL .
				'@@ -1 +1 @@' . PHP_EOL .
				'+' . $data
			)
		;

		$diff->setActual(($data = uniqid()) . PHP_EOL . ($otherSecondString = uniqid()));

		$this->assert
			->castToString($diff)->isEqualTo(
				'-Expected' . PHP_EOL .
				'+Actual' . PHP_EOL .
				'@@ -1 +1,2 @@' . PHP_EOL .
				'+' . $data . PHP_EOL .
				'+' . $otherSecondString
			)
		;

		$diff
			->setExpected($reference = 'check this dokument.')
			->setActual($data = 'check this document.')
		;

		$this->assert
			->castToString($diff)->isEqualTo(
				'-Expected' . PHP_EOL .
				'+Actual' . PHP_EOL .
				'@@ -1 +1 @@' . PHP_EOL .
				'-' . $reference . PHP_EOL .
				'+' . $data
			)
		;

		$diff
			->setExpected($reference = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 4 . PHP_EOL . 5 . PHP_EOL))
			->setActual($data = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 6 . PHP_EOL . 5 . PHP_EOL))
		;

		$this->assert
			->castToString($diff)->isEqualTo(
				'-Expected' . PHP_EOL .
				'+Actual' . PHP_EOL .
				'@@ -4 +4 @@' . PHP_EOL .
				'-4'. PHP_EOL .
				'+6'
			)
		;

		$diff
			->setExpected($reference = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 4 . PHP_EOL . 5 . PHP_EOL))
			->setActual($data = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 6 . PHP_EOL . 7 . PHP_EOL . 5 . PHP_EOL))
		;

		$this->assert
			->castToString($diff)->isEqualTo(
				'-Expected' . PHP_EOL .
				'+Actual' . PHP_EOL .
				'@@ -4 +4,2 @@' . PHP_EOL .
				'-4'. PHP_EOL .
				'+6' . PHP_EOL .
				'+7'
			)
		;
	}

	public function testSetExpected()
	{
		$diff = new tools\diff();

		$this->assert
			->object($diff->setExpected($reference = uniqid()))->isIdenticalTo($diff)
			->string($diff->getExpected())->isEqualTo($reference)
		;
	}

	public function testSetActual()
	{
		$diff = new tools\diff();

		$this->assert
			->object($diff->setActual($data = uniqid()))->isIdenticalTo($diff)
			->string($diff->getActual())->isEqualTo($data)
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
			->array($diff->setActual($data = rand(0, 9))->make())->isEqualTo(array(
					array(
						'-' => array(''),
						'+' => array($data)
					)
				)
			)
			->array($diff->setActual($data = uniqid())->make())->isEqualTo(array(
					array(
						'-' => array(''),
						'+' => array($data)
					)
				)
			)
			->array($diff->setExpected($data)->make())->isEqualTo(array(
					$data
				)
			)
			->array($diff->setExpected('')->setActual(($firstLine = uniqid()). PHP_EOL . ($secondLine = uniqid()))->make())->isEqualTo(array(
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
