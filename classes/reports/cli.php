<?php

namespace mageekguy\atoum\reports;

use \mageekguy\atoum;

class cli extends atoum\report
{
	protected $toWriteFields = array();

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

	public function __toString()
	{
		$string = '';

		foreach ($this->toWriteFields as $field)
		{
			$string .= (string) $field;
		}

		return $string;
	}

	public function runnerStart(atoum\runner $runner)
	{
		return parent::runnerStart($runner)->writeRunnerFields(__FUNCTION__);
	}

	public function testRunStart(atoum\test $test)
	{
		return parent::testRunStart($test)->writeTestFields(__FUNCTION__);
	}

	public function beforeTestSetUp(atoum\test $test)
	{
		return parent::beforeTestSetUp($test)->writeTestFields(__FUNCTION__);
	}

	public function afterTestSetUp(atoum\test $test)
	{
		return parent::afterTestSetUp($test)->writeTestFields(__FUNCTION__);
	}

	public function beforeTestMethod(atoum\test $test)
	{
		return parent::beforeTestMethod($test)->writeTestFields(__FUNCTION__);
	}

	public function testAssertionSuccess(atoum\test $test)
	{
		return parent::testAssertionSuccess($test)->writeTestFields(__FUNCTION__);
	}

	public function testAssertionFail(atoum\test $test)
	{
		return parent::testAssertionFail($test)->writeTestFields(__FUNCTION__);
	}

	public function testError(atoum\test $test)
	{
		return parent::testError($test)->writeTestFields(__FUNCTION__);
	}

	public function testException(atoum\test $test)
	{
		return parent::testException($test)->writeTestFields(__FUNCTION__);
	}

	public function afterTestMethod(atoum\test $test)
	{
		return parent::afterTestMethod($test)->writeTestFields(__FUNCTION__);
	}

	public function testRunStop(atoum\test $test)
	{
		return parent::testRunStop($test)->writeTestFields(__FUNCTION__);
	}

	public function beforeTestTearDown(atoum\test $test)
	{
		return parent::beforeTestTearDown($test)->writeTestFields(__FUNCTION__);
	}

	public function afterTestTearDown(atoum\test $test)
	{
		return parent::afterTestTearDown($test)->writeTestFields(__FUNCTION__);
	}

	public function runnerStop(atoum\runner $runner)
	{
		return parent::runnerStop($runner)->writeRunnerFields(__FUNCTION__);
	}

	protected function writeRunnerFields($event)
	{
		$this->toWriteFields = $this->getRunnerFields($event);
		
		$this->write()->toWriteFields = array();

		return $this;
	}

	protected function writeTestFields($event)
	{
		$this->toWriteFields = $this->getTestFields($event);
		
		$this->write()->toWriteFields = array();

		return $this;
	}
}

?>
