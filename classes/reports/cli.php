<?php

namespace mageekguy\atoum\reports;

use \mageekguy\atoum;

class cli extends atoum\report
{
	public function __construct()
	{
		parent::__construct();

		$this
			->addRunnerField(new atoum\report\fields\runner\version\string(), array(atoum\runner::runStart))
			->addRunnerField(new atoum\report\fields\runner\tests\duration\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\tests\memory\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\tests\coverage\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\duration\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\result\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\failures\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\outputs\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\errors\string(), array(atoum\runner::runStop))
			->addRunnerField(new atoum\report\fields\runner\exceptions\string(), array(atoum\runner::runStop))
			->addTestField(new atoum\report\fields\test\run\string(), array(atoum\test::runStart))
			->addTestField(new atoum\report\fields\test\event\string())
			->addTestField(new atoum\report\fields\test\duration\string(), array(atoum\test::runStop))
			->addTestField(new atoum\report\fields\test\memory\string(), array(atoum\test::runStop))
		;
	}

	public function runnerStart(atoum\runner $runner)
	{
		return parent::runnerStart($runner)->write();
	}

	public function testRunStart(atoum\test $test)
	{
		return parent::testRunStart($test)->write();
	}

	public function beforeTestSetUp(atoum\test $test)
	{
		return parent::beforeTestSetUp($test)->write();
	}

	public function afterTestSetUp(atoum\test $test)
	{
		return parent::afterTestSetUp($test)->write();
	}

	public function beforeTestMethod(atoum\test $test)
	{
		return parent::beforeTestMethod($test)->write();
	}

	public function testAssertionSuccess(atoum\test $test)
	{
		return parent::testAssertionSuccess($test)->write();
	}

	public function testAssertionFail(atoum\test $test)
	{
		return parent::testAssertionFail($test)->write();
	}

	public function testError(atoum\test $test)
	{
		return parent::testError($test)->write();
	}

	public function testException(atoum\test $test)
	{
		return parent::testException($test)->write();
	}

	public function afterTestMethod(atoum\test $test)
	{
		return parent::afterTestMethod($test)->write();
	}

	public function testRunStop(atoum\test $test)
	{
		return parent::testRunStop($test)->write();
	}

	public function beforeTestTearDown(atoum\test $test)
	{
		return parent::beforeTestTearDown($test)->write();
	}

	public function afterTestTearDown(atoum\test $test)
	{
		return parent::afterTestTearDown($test)->write();
	}

	public function runnerStop(atoum\runner $runner)
	{
		return parent::runnerStop($runner)->write();
	}
}

?>
