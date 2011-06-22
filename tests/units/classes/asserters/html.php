<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	\mageekguy\atoum,
	\mageekguy\atoum\asserter,
	\mageekguy\atoum\asserters,
	\mageekguy\atoum\tools\diffs,
	\mageekguy\atoum\tests\functional\selenium
;

require_once(__DIR__ . '/../../runner.php');

class html extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('\mageekguy\atoum\asserter')
		;
	}

	public function test__construct()
	{
		$asserter = new asserters\html($generator = new asserter\generator($this));

		$this->assert
			->object($asserter->getScore())->isIdenticalTo($this->getScore())
			->object($asserter->getLocale())->isIdenticalTo($this->getLocale())
			->object($asserter->getGenerator())->isIdenticalTo($generator)
			->boolean($asserter->wasSet())->isFalse()
		;
	}

	public function testSetWith()
	{
		$asserter = new asserters\html(new asserter\generator($test = new self($score = new atoum\score())));

		$this->assert
			->exception(function() use (& $line, $asserter, & $value) { $line = __LINE__; $asserter->setWith($value = uniqid()); })
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not an instance of selenium\html'), $asserter->getTypeOf($value)))
			->array($score->getFailAssertions())->isEqualTo(array(
					array(
						'case' => null,
						'class' => __CLASS__,
						'method' => $test->getCurrentMethod(),
						'file' => __FILE__,
						'line' => $line,
						'asserter' => get_class($asserter) . '::setWith()',
						'fail' => sprintf($test->getLocale()->_('%s is not an instance of selenium\html'), $asserter->getTypeOf($value))
					)
				)
			)
		;
	}

	public function testHasTitle()
	{
		$this->mock('\mageekguy\atoum\tests\functional\selenium\html');
		$html = new atoum\mock\mageekguy\atoum\tests\functional\selenium\html('http://www.atoum.org');
		$html->getMockController()->getTitle = 'Atoum website title';

		$asserter = new asserters\html(new asserter\generator($test = new self($score = new atoum\score())));
		$asserter->setWith($html);

		$title = 'wrong title';

		$diff = new diffs\variable();
		$diff->setReference($title)->setData($html->getTitle());

		$this->assert
			->exception(function() use ($asserter, $title, & $line) {
						$line = __LINE__; $asserter->hasTitle($title);
					}
			)
				->isInstanceOf('\mageekguy\atoum\asserter\exception')
				->hasMessage(sprintf($test->getLocale()->_('%s is not equal to %s'), $asserter->getTypeOf($html->getTitle()), $asserter->getTypeOf($title)) . PHP_EOL . $diff)
			->integer($score->getPassNumber())->isEqualTo(1)
			->integer($score->getFailNumber())->isEqualTo(1)
			->array($score->getFailAssertions())->isEqualTo(array(
						array(
							'case' => null,
							'class' => __CLASS__,
							'method' => $test->getCurrentMethod(),
							'file' => __FILE__,
							'line' => $line,
							'asserter' => get_class($asserter) . '::hasTitle()',
							'fail' => sprintf($test->getLocale()->_('%s is not equal to %s'), $asserter->getTypeOf($html->getTitle()), $asserter->getTypeOf($title) . PHP_EOL . $diff)
						)
					)
				)
			;
	}
}

?>
