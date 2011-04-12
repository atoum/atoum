<?php

namespace mageekguy\atoum\reports;

use
	\mageekguy\atoum,
	\mageekguy\atoum\report
;

abstract class realtime extends atoum\report
{
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

	public function addWriter(report\writers\realtime $writer)
	{
		$this->writers[] = $writer;

		return $this;
	}

	public function runnerStop(atoum\runner $runner)
	{
		return parent::runnerStop($runner)->write();
	}
}

?>
