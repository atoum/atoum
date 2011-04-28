<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	\mageekguy\atoum,
	\mageekguy\atoum\mock,
	\mageekguy\atoum\scripts
;

require_once(__DIR__ . '/../../runner.php');

class tagger extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\script')
		;
	}

	public function test__construct()
	{
		$tagger = new scripts\tagger(uniqid());

		$this->assert
			->variable($tagger->getSourceDirectory())->isNull()
		;
	}

	public function testSetSourceDirectory()
	{
		$tagger = new scripts\tagger(uniqid());

		$this->assert
			->object($tagger->setSourceDirectory($directory = uniqid()))->isIdenticalTo($tagger)
			->string($tagger->getSourceDirectory())->isEqualTo($directory)
			->object($tagger->setSourceDirectory($directory = rand(- PHP_INT_MAX, PHP_INT_MAX)))->isIdenticalTo($tagger)
			->string($tagger->getSourceDirectory())->isEqualTo((string) $directory)
		;
	}

	public function testGetFilesIterator()
	{
		$tagger = new scripts\tagger(uniqid());

		$this->assert
			->exception(function() use ($tagger) {
					$tagger->getFilesIterator();
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic')
				->hasMessage('Unable to get files iterator, source directory is undefined')
		;
	}
}

?>
