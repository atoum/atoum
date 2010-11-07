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
					atoum\runner::runStart => array(),
					atoum\runner::runStop => array()
				)
			)
			->array($report->getTestFields())->isEqualTo(array(
					atoum\test::runStart => array(),
					atoum\test::beforeSetUp => array(),
					atoum\test::afterSetUp => array(),
					atoum\test::beforeTestMethod => array(),
					atoum\test::success => array(),
					atoum\test::fail => array(),
					atoum\test::error => array(),
					atoum\test::exception => array(),
					atoum\test::afterTestMethod => array(),
					atoum\test::beforeTearDown => array(),
					atoum\test::afterTearDown => array(),
					atoum\test::runStop => array()
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
					atoum\test::runStart => array(),
					atoum\test::beforeSetUp => array(),
					atoum\test::afterSetUp => array(),
					atoum\test::beforeTestMethod => array(),
					atoum\test::success => array(),
					atoum\test::fail => array(),
					atoum\test::error => array(),
					atoum\test::exception => array(),
					atoum\test::afterTestMethod => array(),
					atoum\test::beforeTearDown => array(),
					atoum\test::afterTearDown => array(),
					atoum\test::runStop => array()
				)
			)
			->object($report->addTestField($field = new mock\mageekguy\atoum\report\fields\test()))->isIdenticalTo($report)
			->array($report->getTestFields())->isIdenticalTo(array(
					atoum\test::runStart => array($field),
					atoum\test::beforeSetUp => array($field),
					atoum\test::afterSetUp => array($field),
					atoum\test::beforeTestMethod => array($field),
					atoum\test::success => array($field),
					atoum\test::fail => array($field),
					atoum\test::error => array($field),
					atoum\test::exception => array($field),
					atoum\test::afterTestMethod => array($field),
					atoum\test::beforeTearDown => array($field),
					atoum\test::afterTearDown => array($field),
					atoum\test::runStop => array($field)
				)
			)
			->object($report->addTestField($otherField = new mock\mageekguy\atoum\report\fields\test()))->isIdenticalTo($report)
			->array($report->getTestFields())->isIdenticalTo(array(
					atoum\test::runStart => array($field, $otherField),
					atoum\test::beforeSetUp => array($field, $otherField),
					atoum\test::afterSetUp => array($field, $otherField),
					atoum\test::beforeTestMethod => array($field, $otherField),
					atoum\test::success => array($field, $otherField),
					atoum\test::fail => array($field, $otherField),
					atoum\test::error => array($field, $otherField),
					atoum\test::exception => array($field, $otherField),
					atoum\test::afterTestMethod => array($field, $otherField),
					atoum\test::beforeTearDown => array($field, $otherField),
					atoum\test::afterTearDown => array($field, $otherField),
					atoum\test::runStop => array($field, $otherField)
				)
			)
			->object($report->addTestField($beforeTestSetUpField = new mock\mageekguy\atoum\report\fields\test(), array(atoum\test::beforeSetUp)))->isIdenticalTo($report)
			->array($report->getTestFields())->isIdenticalTo(array(
					atoum\test::runStart => array($field, $otherField),
					atoum\test::beforeSetUp => array($field, $otherField, $beforeTestSetUpField),
					atoum\test::afterSetUp => array($field, $otherField),
					atoum\test::beforeTestMethod => array($field, $otherField),
					atoum\test::success => array($field, $otherField),
					atoum\test::fail => array($field, $otherField),
					atoum\test::error => array($field, $otherField),
					atoum\test::exception => array($field, $otherField),
					atoum\test::afterTestMethod => array($field, $otherField),
					atoum\test::beforeTearDown => array($field, $otherField),
					atoum\test::afterTearDown => array($field, $otherField),
					atoum\test::runStop => array($field, $otherField)
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

	public function testGetRunnerFields()
	{
		$report = new atoum\report();

		$this->assert
			->array($report->getRunnerFields())->isEqualTo(array(
					atoum\runner::runStart => array(),
					atoum\runner::runStop => array()
				)
			)
		;

		$this->assert
			->array($report->getRunnerFields(atoum\runner::runStart))->isEmpty()
			->array($report->getRunnerFields(atoum\runner::runStop))->isEmpty()
		;
	}

	public function testGetTestFields()
	{
		$report = new atoum\report();

		$this->assert
			->array($report->getTestFields())->isEqualTo(array(
					atoum\test::runStart => array(),
					atoum\test::beforeSetUp => array(),
					atoum\test::afterSetUp => array(),
					atoum\test::beforeTestMethod => array(),
					atoum\test::success => array(),
					atoum\test::fail => array(),
					atoum\test::error => array(),
					atoum\test::exception => array(),
					atoum\test::afterTestMethod => array(),
					atoum\test::beforeTearDown => array(),
					atoum\test::afterTearDown => array(),
					atoum\test::runStop => array()
				)
			)
		;

		$this->assert
			->array($report->getTestFields(atoum\test::runStart))->isEmpty()
			->array($report->getTestFields(atoum\test::beforeSetUp))->isEmpty()
			->array($report->getTestFields(atoum\test::afterSetUp))->isEmpty()
			->array($report->getTestFields(atoum\test::beforeTestMethod))->isEmpty()
			->array($report->getTestFields(atoum\test::success))->isEmpty()
			->array($report->getTestFields(atoum\test::fail))->isEmpty()
			->array($report->getTestFields(atoum\test::error))->isEmpty()
			->array($report->getTestFields(atoum\test::exception))->isEmpty()
			->array($report->getTestFields(atoum\test::afterTestMethod))->isEmpty()
			->array($report->getTestFields(atoum\test::beforeTearDown))->isEmpty()
			->array($report->getTestFields(atoum\test::afterTearDown))->isEmpty()
			->array($report->getTestFields(atoum\test::runStop))->isEmpty()
		;
	}
}

?>
