<?php

namespace mageekguy\atoum\tests\units\tools\diff;

use
	atoum,
	atoum\tools
;

require_once __DIR__ . '/../../../runner.php';

class decorator extends atoum
{
	public function testDecorate()
	{
		$this
			->given($this->newTestedInstance)

			->if($diff = new tools\diff())
			->then
				->string($this->testedInstance->decorate($diff))->isEmpty()

			->if($diff->setActual($data = uniqid()))
			->then
				->string($this->testedInstance->decorate($diff))->isEqualTo(
					'-Expected' . PHP_EOL .
					'+Actual' . PHP_EOL .
					'@@ -1 +1 @@' . PHP_EOL .
					'+' . $data
				)

			->if($diff->setActual(($data = uniqid()) . PHP_EOL . ($otherSecondString = uniqid())))
			->then
				->string($this->testedInstance->decorate($diff))->isEqualTo(
					'-Expected' . PHP_EOL .
					'+Actual' . PHP_EOL .
					'@@ -1 +1,2 @@' . PHP_EOL .
					'+' . $data . PHP_EOL .
					'+' . $otherSecondString
				)

			->if($diff
				->setExpected($reference = 'check this dokument.')
				->setActual($data = 'check this document.')
			)
			->then
				->string($this->testedInstance->decorate($diff))->isEqualTo(
					'-Expected' . PHP_EOL .
					'+Actual' . PHP_EOL .
					'@@ -1 +1 @@' . PHP_EOL .
					'-' . $reference . PHP_EOL .
					'+' . $data
				)

			->if($diff
				->setExpected($reference = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 4 . PHP_EOL . 5 . PHP_EOL))
				->setActual($data = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 6 . PHP_EOL . 5 . PHP_EOL))
			)
			->then
				->string($this->testedInstance->decorate($diff))->isEqualTo(
					'-Expected' . PHP_EOL .
					'+Actual' . PHP_EOL .
					'@@ -4 +4 @@' . PHP_EOL .
					'-4'. PHP_EOL .
					'+6'
				)

			->if($diff
				->setExpected($reference = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 4 . PHP_EOL . 5 . PHP_EOL))
				->setActual($data = (1 . PHP_EOL . 2 . PHP_EOL . 3 . PHP_EOL . 6 . PHP_EOL . 7 . PHP_EOL . 5 . PHP_EOL))
			)
			->then
				->string($this->testedInstance->decorate($diff))->isEqualTo(
					'-Expected' . PHP_EOL .
					'+Actual' . PHP_EOL .
					'@@ -4 +4,2 @@' . PHP_EOL .
					'-4'. PHP_EOL .
					'+6' . PHP_EOL .
					'+7'
				)
		;
	}
}
