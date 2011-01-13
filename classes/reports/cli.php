<?php

namespace mageekguy\atoum\reports;

use \mageekguy\atoum;

class cli extends atoum\report
{
	protected $triggeredFields = array();

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

		foreach ($this->triggeredFields as $field)
		{
			$string .= (string) $field;
		}

		return $string;
	}

	public function runnerStart(atoum\runner $runner)
	{
		parent::runnerStart($runner);

		$this->triggeredFields = $this->getRunnerFields(__FUNCTION__);
			
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function testRunStart(atoum\test $test)
	{
		parent::testRunStart($test);

		$this->triggeredFields = $this->getTestFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function beforeTestSetUp(atoum\test $test)
	{
		parent::beforeTestSetUp($test);
		
		$this->triggeredFields = $this->getTestFields(__FUNCTION__);

		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function afterTestSetUp(atoum\test $test)
	{
		parent::afterTestSetUp($test);
		
		$this->triggeredFields = $this->getTestFields(__FUNCTION__);

		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function beforeTestMethod(atoum\test $test)
	{
		parent::beforeTestMethod($test);

		$this->triggeredFields = $this->getTestFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function testAssertionSuccess(atoum\test $test)
	{
		parent::testAssertionSuccess($test);
		
		$this->triggeredFields = $this->getTestFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function testAssertionFail(atoum\test $test)
	{
		parent::testAssertionFail($test);
		
		$this->triggeredFields = $this->getTestFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function testError(atoum\test $test)
	{
		parent::testError($test);
		
		$this->triggeredFields = $this->getTestFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function testException(atoum\test $test)
	{
		parent::testException($test);
		
		$this->triggeredFields = $this->getTestFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function afterTestMethod(atoum\test $test)
	{
		parent::afterTestMethod($test);
		
		$this->triggeredFields = $this->getTestFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function testRunStop(atoum\test $test)
	{
		parent::testRunStop($test);
		
		$this->triggeredFields = $this->getTestFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function beforeTestTearDown(atoum\test $test)
	{
		parent::beforeTestTearDown($test);
		
		$this->triggeredFields = $this->getTestFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function afterTestTearDown(atoum\test $test)
	{
		parent::afterTestTearDown($test);
		
		$this->triggeredFields = $this->getTestFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}

	public function runnerStop(atoum\runner $runner)
	{
		parent::runnerStop($runner);

		$this->triggeredFields = $this->getRunnerFields(__FUNCTION__);
		
		$this->write();

		$this->triggeredFields = array();

		return $this;
	}
}

?>
