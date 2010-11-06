<?php

namespace mageekguy\atoum\tests\units\tools;

use \mageekguy\atoum;
use \mageekguy\atoum\tools;

require_once(__DIR__ . '/../../runner.php');

class diff extends atoum\test
{
	public function test__construct()
	{
		$diff = new tools\diff();

		$this->assert
			->string($diff->getFirstString())->isEmpty()
			->string($diff->getSecondString())->isEmpty()
		;

		$diff = new tools\diff($firstString = uniqid());

		$this->assert
			->string($diff->getFirstString())->isEqualTo($firstString)
			->string($diff->getSecondString())->isEmpty()
		;
	}

	public function test__toString()
	{
		$diff = new tools\diff();

		$this->assert
			->castToString($diff)->isEmpty()
		;

		$diff->setSecondString($secondString = uniqid());

		$this->assert
			->castToString($diff)->isEqualTo(
				'@@ -1 +1 @@' . PHP_EOL .
				'+' . $secondString . PHP_EOL
			)
		;

		$diff->setSecondString(($secondString = uniqid()) . PHP_EOL . ($otherSecondString = uniqid()));

		$this->assert
			->castToString($diff)->isEqualTo(
				'@@ -1 +1,2 @@' . PHP_EOL .
				'+' . $secondString . PHP_EOL .
				'+' . $otherSecondString . PHP_EOL
			)
		;

		$diff
			->setFirstString($firstString = 'check this dokument.')
			->setSecondString($secondString = 'check this document.')
		;

		$this->assert
			->castToString($diff)->isEqualTo(
				'@@ -1 +1 @@' . PHP_EOL .
				'-' . $firstString . PHP_EOL .
				'+' . $secondString . PHP_EOL
			)
		;

		$diff
			->setFirstString($firstString = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 4 . PHP_EOL . 5 . PHP_EOL))
			->setSecondString($secondString = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 6 . PHP_EOL . 5 . PHP_EOL))
		;

		$this->assert
			->castToString($diff)->isEqualTo(
				'@@ -4 +4 @@' . PHP_EOL .
				'-4'. PHP_EOL .
				'+6' . PHP_EOL
			)
		;

		$diff
			->setFirstString($firstString = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 4 . PHP_EOL . 5 . PHP_EOL))
			->setSecondString($secondString = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 6 . PHP_EOL . 7 . PHP_EOL . 5 . PHP_EOL))
		;

		$this->assert
			->castToString($diff)->isEqualTo(
				'@@ -4 +4,2 @@' . PHP_EOL .
				'-4'. PHP_EOL .
				'+6' . PHP_EOL .
				'+7' . PHP_EOL
			)
		;
	}

	public function testMake()
	{
		$diff = new tools\diff('', '');

		$this->assert
			->array($diff->make())->isEqualTo(array(
					''
				)
			)
			->array($diff->setSecondString($secondString = rand(0, 9))->make())->isEqualTo(array(
					array(
						'-' => array(''),
						'+' => array($secondString)
					)
				)
			)
			->array($diff->setSecondString($secondString = uniqid())->make())->isEqualTo(array(
					array(
						'-' => array(''),
						'+' => array($secondString)
					)
				)
			)
			->array($diff->setFirstString($secondString)->make())->isEqualTo(array(
					$secondString
				)
			)
			->array($diff->setFirstString('')->setSecondString(($firstLine = uniqid()). PHP_EOL . ($secondLine = uniqid()))->make())->isEqualTo(array(
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

?>
