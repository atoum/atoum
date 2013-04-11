<?php

namespace mageekguy\atoum\tests\units\report\fields\runner\result;

use
	mageekguy\atoum,
	mageekguy\atoum\locale,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\report\fields\runner\result\logo as testedClass
;

require_once __DIR__ . '/../../../../../runner.php';

class logo extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\report\fields\runner\result\cli');
	}

	public function test__toString()
	{
		$score = new \mock\mageekguy\atoum\score();
		$scoreController = $score->getMockController();
		$scoreController->getAssertionNumber = 1;
		$scoreController->getFailNumber = 0;
		$scoreController->getErrorNumber = 0;
		$scoreController->getExceptionNumber = 0;

		$runner = new \mock\mageekguy\atoum\runner();
		$runnerController = $runner->getMockController();
		$runnerController->getScore = $score;
		$runnerController->getTestNumber = 1;
		$runnerController->getTestMethodNumber = 1;

		$this->startCase('Success with one test, one method and one assertion, no fail, no error, no exception');

		$this
			->if($field = new testedClass())
            ->and($field->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($field)->isEqualTo("
              \033[48;5;16m  \033[0m                                 \033[48;5;16m  \033[0m
            \033[48;5;16m    \033[0m                                 \033[48;5;16m   \033[0m
            \033[48;5;16m  \033[48;5;231m \033[48;5;120m \033[48;5;16m  \033[0m                             \033[48;5;16m  \033[48;5;120m \033[48;5;231m \033[48;5;16m \033[0m
            \033[48;5;16m  \033[48;5;231m \033[48;5;120m   \033[48;5;16m                             \033[48;5;120m   \033[48;5;231m \033[48;5;16m \033[0m
            \033[48;5;16m  \033[48;5;231m \033[48;5;120m            \033[48;5;16m  \033[48;5;157m       \033[48;5;16m  \033[48;5;120m            \033[48;5;231m \033[48;5;16m \033[0m
	    \033[48;5;16m  \033[48;5;34m           \033[48;5;16m  \033[48;5;157m \033[48;5;120m         \033[48;5;157m \033[48;5;16m  \033[48;5;34m           \033[48;5;16m \033[0m
              \033[48;5;16m           \033[48;5;157m \033[48;5;120m             \033[48;5;157m \033[48;5;16m           \033[0m
                       \033[48;5;16m  \033[48;5;157m \033[48;5;120m             \033[48;5;157m \033[48;5;16m  \033[0m
                      \033[48;5;16m   \033[48;5;157m \033[48;5;120m   \033[48;5;16m  \033[48;5;120m   \033[48;5;16m  \033[48;5;120m   \033[48;5;157m \033[48;5;16m   \033[0m
                    \033[48;5;16m  \033[48;5;83m \033[48;5;16m  \033[48;5;157m \033[48;5;120m   \033[48;5;16m  \033[48;5;120m   \033[48;5;16m  \033[48;5;120m   \033[48;5;157m \033[48;5;16m  \033[48;5;83m \033[48;5;16m  \033[0m
                    \033[48;5;16m     \033[48;5;157m \033[48;5;120m             \033[48;5;157m \033[48;5;16m     \033[0m
                       \033[48;5;16m    \033[48;5;157m \033[48;5;120m         \033[48;5;157m \033[48;5;16m    \033[0m
                         \033[48;5;16m  \033[48;5;157m \033[48;5;120m         \033[48;5;157m \033[48;5;16m  \033[0m
                         \033[48;5;16m  \033[48;5;83m           \033[48;5;16m  \033[0m
                         \033[48;5;16m  \033[48;5;83m  \033[48;5;16m   \033[48;5;83m \033[48;5;16m   \033[48;5;83m  \033[48;5;16m  \033[0m
                           \033[48;5;16m  \033[48;5;83m       \033[48;5;16m  \033[0m
                             \033[48;5;16m       \033[0m
            \033[0m" . PHP_EOL)
		;

		$this->startCase('Failure with several tests, several methods and several assertions, one fail, one error, one exception');

		$scoreController->getFailNumber = 1;
		$scoreController->getErrorNumber = 1;
		$scoreController->getExceptionNumber = 1;
		$scoreController->getUncompletedMethodNumber = 1;

		$this
			->if($field = new testedClass())
			->and($field->handleEvent(atoum\runner::runStop, $runner))
			->then
				->castToString($field)->isEqualTo("
              \033[48;5;16m  \033[0m                                 \033[48;5;16m  \033[0m
            \033[48;5;16m    \033[0m                                 \033[48;5;16m   \033[0m
            \033[48;5;16m  \033[48;5;231m \033[48;5;211m \033[48;5;16m  \033[0m                             \033[48;5;16m  \033[48;5;211m \033[48;5;231m \033[48;5;16m \033[0m
            \033[48;5;16m  \033[48;5;231m \033[48;5;211m   \033[48;5;16m                             \033[48;5;211m   \033[48;5;231m \033[48;5;16m \033[0m
            \033[48;5;16m  \033[48;5;231m \033[48;5;211m            \033[48;5;16m  \033[48;5;218m       \033[48;5;16m  \033[48;5;211m            \033[48;5;231m \033[48;5;16m \033[0m
            \033[48;5;16m  \033[48;5;124m           \033[48;5;16m  \033[48;5;218m \033[48;5;204m         \033[48;5;218m \033[48;5;16m  \033[48;5;124m           \033[48;5;16m \033[0m
              \033[48;5;16m           \033[48;5;218m \033[48;5;204m             \033[48;5;218m \033[48;5;16m           \033[0m \033[0m
                       \033[48;5;16m  \033[48;5;218m \033[48;5;204m             \033[48;5;218m \033[48;5;16m \033[48;5;16m \033[0m
                      \033[48;5;16m   \033[48;5;218m \033[48;5;204m   \033[48;5;16m  \033[48;5;204m   \033[48;5;16m  \033[48;5;204m   \033[48;5;218m \033[48;5;16m   \033[0m
                    \033[48;5;16m  \033[48;5;197m \033[48;5;16m  \033[48;5;218m \033[48;5;204m   \033[48;5;16m  \033[48;5;204m   \033[48;5;16m  \033[48;5;204m   \033[48;5;218m \033[48;5;16m  \033[48;5;197m \033[48;5;16m  \033[0m
                    \033[48;5;16m     \033[48;5;218m \033[48;5;204m             \033[48;5;218m \033[48;5;16m     \033[0m
                       \033[48;5;16m    \033[48;5;218m \033[48;5;204m         \033[48;5;218m \033[48;5;16m    \033[0m
                         \033[48;5;16m  \033[48;5;218m \033[48;5;204m         \033[48;5;218m \033[48;5;16m  \033[0m
                         \033[48;5;16m  \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;16m  \033[0m
                         \033[48;5;16m  \033[48;5;197m \033[48;5;197m \033[48;5;16m   \033[48;5;197m \033[48;5;16m   \033[48;5;197m \033[48;5;197m \033[48;5;16m  \033[0m
                           \033[48;5;16m  \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;197m \033[48;5;16m  \033[0m
                             \033[48;5;16m       \033[0m
            \033[0m" . PHP_EOL)
		;
	}
}
