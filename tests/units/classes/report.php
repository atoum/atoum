<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;
use \mageekguy\atoum\mock;

require_once(__DIR__ . '/../runner.php');

class report extends atoum\test
{
	public function test__construct()
	{
		$report = new atoum\report();

		$this->assert
			->object($report)
				->isInstanceOf('\mageekguy\atoum\observers\runner')
				->isInstanceOf('\mageekguy\atoum\observers\test')
			->array($report->getRunnerFields())->isEqualTo(array(
					'runnerStart' => array(),
					'runnerStop' => array()
				)
			)
			->array($report->getTestFields())->isEqualTo(array(
					'testRunnerStart' => array(),
					'beforeTestSetup' => array(),
					'afterTestSetup' => array(),
					'beforeTestMethod' => array(),
					'testAssertionSuccess' => array(),
					'testAssertionFail' => array(),
					'testError' => array(),
					'testException' => array(),
					'afterTestMethod' => array(),
					'beforeTestTearDown' => array(),
					'afterTestTearDown' => array()
				)
			)
			->array($report->getDecorators())->isEmpty()
		;
	}

	public function testAddRunnerField()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\report\fields\runner');

		$report = new atoum\report();

		$this->assert
			->array($report->getRunnerFields())->isEqualTo(array(
					'runnerStart' => array(),
					'runnerStop' => array()
				)
			)
			->object($report->addRunnerField($field = new mock\mageekguy\atoum\report\fields\runner()))->isIdenticalTo($report)
			->array($report->getRunnerFields())->isIdenticalTo(array(
					'runnerStart' => array($field),
					'runnerStop' => array($field)
				)
			)
			->object($report->addRunnerField($otherField = new mock\mageekguy\atoum\report\fields\runner()))->isIdenticalTo($report)
			->array($report->getRunnerFields())->isIdenticalTo(array(
					'runnerStart' => array($field, $otherField),
					'runnerStop' => array($field, $otherField)
				)
			)
			->object($report->addRunnerField($runnerStopField = new mock\mageekguy\atoum\report\fields\runner(), array('runnerStop')))->isIdenticalTo($report)
			->array($report->getRunnerFields())->isIdenticalTo(array(
					'runnerStart' => array($field, $otherField),
					'runnerStop' => array($field, $otherField, $runnerStopField)
				)
			)
		;

		$this->assert
			->exception(function() use ($report, & $event) {
					$report->addRunnerField(new mock\mageekguy\atoum\report\fields\runner(), array($event = uniqid()));
				}
			)
				->isInstanceOf('\logicException')
				->hasMessage('Event \'' . $event . '\' does not exist')
		;
	}

	public function testAddTestField()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\report\fields\test');

		$report = new atoum\report();

		$this->assert
			->array($report->getTestFields())->isEqualTo(array(
					'testRunnerStart' => array(),
					'beforeTestSetup' => array(),
					'afterTestSetup' => array(),
					'beforeTestMethod' => array(),
					'testAssertionSuccess' => array(),
					'testAssertionFail' => array(),
					'testError' => array(),
					'testException' => array(),
					'afterTestMethod' => array(),
					'beforeTestTearDown' => array(),
					'afterTestTearDown' => array()
				)
			)
			->object($report->addTestField($field = new mock\mageekguy\atoum\report\fields\test()))->isIdenticalTo($report)
			->array($report->getTestFields())->isIdenticalTo(array(
					'testRunnerStart' => array($field),
					'beforeTestSetup' => array($field),
					'afterTestSetup' => array($field),
					'beforeTestMethod' => array($field),
					'testAssertionSuccess' => array($field),
					'testAssertionFail' => array($field),
					'testError' => array($field),
					'testException' => array($field),
					'afterTestMethod' => array($field),
					'beforeTestTearDown' => array($field),
					'afterTestTearDown' => array($field)
				)
			)
			->object($report->addTestField($otherField = new mock\mageekguy\atoum\report\fields\test()))->isIdenticalTo($report)
			->array($report->getTestFields())->isIdenticalTo(array(
					'testRunnerStart' => array($field, $otherField),
					'beforeTestSetup' => array($field, $otherField),
					'afterTestSetup' => array($field, $otherField),
					'beforeTestMethod' => array($field, $otherField),
					'testAssertionSuccess' => array($field, $otherField),
					'testAssertionFail' => array($field, $otherField),
					'testError' => array($field, $otherField),
					'testException' => array($field, $otherField),
					'afterTestMethod' => array($field, $otherField),
					'beforeTestTearDown' => array($field, $otherField),
					'afterTestTearDown' => array($field, $otherField)
				)
			)
			->object($report->addTestField($beforeTestSetupField = new mock\mageekguy\atoum\report\fields\test(), array('beforeTestSetup')))->isIdenticalTo($report)
			->array($report->getTestFields())->isIdenticalTo(array(
					'testRunnerStart' => array($field, $otherField),
					'beforeTestSetup' => array($field, $otherField, $beforeTestSetupField),
					'afterTestSetup' => array($field, $otherField),
					'beforeTestMethod' => array($field, $otherField),
					'testAssertionSuccess' => array($field, $otherField),
					'testAssertionFail' => array($field, $otherField),
					'testError' => array($field, $otherField),
					'testException' => array($field, $otherField),
					'afterTestMethod' => array($field, $otherField),
					'beforeTestTearDown' => array($field, $otherField),
					'afterTestTearDown' => array($field, $otherField)
				)
			)
		;
	}

	public function testAddDecorator()
	{
		$mockGenerator = new mock\generator();
		$mockGenerator->generate('\mageekguy\atoum\report\decorator');

		$report = new atoum\report();

		$this->assert
			->array($report->getDecorators())->isEmpty()
			->object($report->addDecorator($decorator = new mock\mageekguy\atoum\report\decorator()))->isIdenticalTo($report)
			->array($report->getDecorators())->isIdenticalTo(array($decorator))
			->object($report->addDecorator($otherDecorator = new mock\mageekguy\atoum\report\decorator()))->isIdenticalTo($report)
			->array($report->getDecorators())->isIdenticalTo(array($decorator, $otherDecorator))
		;
	}

	public function testRunnerStart()
	{
		$report = new atoum\report();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\report\fields\runner')
			->generate('\mageekguy\atoum\report\decorator')
		;

		$field = new mock\mageekguy\atoum\report\fields\runner();
		$field->getMockController()->setWithRunner = function() use ($field) { return $field; };

		$otherField = new mock\mageekguy\atoum\report\fields\runner();
		$otherField->getMockController()->setWithRunner = function() use ($otherField) { return $otherField; };

		$report
			->addRunnerField($field)
			->addRunnerField($otherField)
			->addDecorator($decorator = new mock\mageekguy\atoum\report\decorator())
			->addDecorator($otherDecorator = new mock\mageekguy\atoum\report\decorator())
		;

		$write = function() {};
		$decorator->getMockController()->write = $write;
		$otherDecorator->getMockController()->write = $write;

		$this->assert
			->object($report->runnerStart($runner = new atoum\runner()))->isIdenticalTo($report)
			->mock($decorator)
				->call('write', array($field))
				->call('write', array($otherField))
			->mock($otherDecorator)
				->call('write', array($field))
				->call('write', array($otherField))
		;
	}

	public function testRunnerStop()
	{
		$report = new atoum\report();

		$mockGenerator = new mock\generator();
		$mockGenerator
			->generate('\mageekguy\atoum\report\fields\runner')
			->generate('\mageekguy\atoum\report\decorator')
		;

		$field = new mock\mageekguy\atoum\report\fields\runner();
		$field->getMockController()->setWithRunner = function() use ($field) { return $field; };

		$otherField = new mock\mageekguy\atoum\report\fields\runner();
		$otherField->getMockController()->setWithRunner = function() use ($otherField) { return $otherField; };


		$report
			->addRunnerField($field)
			->addRunnerField($otherField)
			->addDecorator($decorator = new mock\mageekguy\atoum\report\decorator())
			->addDecorator($otherDecorator = new mock\mageekguy\atoum\report\decorator())
		;

		$flush = function() {};
		$decorator->getMockController()->flush = $flush;
		$otherDecorator->getMockController()->flush = $flush;

		$this->assert
			->object($report->runnerStop($runner = new atoum\runner()))->isIdenticalTo($report)
			->mock($decorator)
				->call('flush', array($field))
				->call('flush', array($otherField))
			->mock($otherDecorator)
				->call('flush', array($field))
				->call('flush', array($otherField))
		;
	}
}

?>
