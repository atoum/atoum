<?php

namespace mageekguy\atoum\reports;

use
	mageekguy\atoum,
	mageekguy\atoum\report
;

abstract class asynchronous extends atoum\report
{
	protected $string = '';
	protected $fail = false;

	public function __toString()
	{
		return $this->string;
	}

	public function runnerStart(atoum\runner $runner)
	{
		$this->string = parent::runnerStart($runner)->getRunnerFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function testRunStart(atoum\test $test)
	{
		$this->string .= parent::testRunStart($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function beforeTestSetUp(atoum\test $test)
	{
		$this->string .= parent::beforeTestSetUp($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function afterTestSetUp(atoum\test $test)
	{
		$this->string .= parent::afterTestSetUp($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function beforeTestMethod(atoum\test $test)
	{
		$this->string .= parent::beforeTestMethod($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function testAssertionSuccess(atoum\test $test)
	{
		$this->string .= parent::testAssertionSuccess($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function testAssertionFail(atoum\test $test)
	{
		$this->fail = true;

		$this->string .= parent::testAssertionFail($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function testError(atoum\test $test)
	{
		$this->fail = true;

		$this->string .= parent::testError($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function testException(atoum\test $test)
	{
		$this->fail = true;

		$this->string .= parent::testException($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function afterTestMethod(atoum\test $test)
	{
		$this->string .= parent::afterTestMethod($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function testRunStop(atoum\test $test)
	{
		$this->string .= parent::testRunStop($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function beforeTestTearDown(atoum\test $test)
	{
		$this->string .= parent::beforeTestTearDown($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function afterTestTearDown(atoum\test $test)
	{
		$this->string .= parent::afterTestTearDown($test)->getTestFieldsAsString(__FUNCTION__);

		return $this;
	}

	public function runnerStop(atoum\runner $runner)
	{
		$this->string .= parent::runnerStop($runner)->getRunnerFieldsAsString(__FUNCTION__);

		if ($this->title !== null)
		{
			$this->title = sprintf($this->title, $this->adapter->date($this->locale->_('Y-m-d')), $this->adapter->date($this->locale->_('H:i:s')), $this->fail === true ? $this->locale->_('FAIL') : $this->locale->_('SUCCESS'));
		}

		foreach ($this->writers as $writer)
		{
			$writer->writeAsynchronousReport($this);
		}

		return $this;
	}

	public function getRunnerFieldsAsString($event)
	{
		return $this->getFieldsAsString($this->runnerFields, $event);
	}

	public function getTestFieldsAsString($event)
	{
		return $this->getFieldsAsString($this->testFields, $event);
	}

	public function addWriter(report\writers\asynchronous $writer)
	{
		return $this->doAddWriter($writer);
	}

	protected function getFieldsAsString(array $fields, $event)
	{
		$string = '';

		if (isset($fields[$event]) === true)
		{
			foreach ($fields[$event] as $field)
			{
				$string .= (string) $field;
			}
		}

		return $string;
	}
}

?>
