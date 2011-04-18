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
		if ($this->title !== null)
		{
			$this->title = sprintf($this->title, $this->adapter->date($this->locale->_('Y-m-d')), $this->adapter->date($this->locale->_('H:i:s')));
		}

		return parent::runnerStart($runner)->write(__FUNCTION__);
	}

	public function testRunStart(atoum\test $test)
	{
		return parent::testRunStart($test)->write(__FUNCTION__);
	}

	public function beforeTestSetUp(atoum\test $test)
	{
		return parent::beforeTestSetUp($test)->write(__FUNCTION__);
	}

	public function afterTestSetUp(atoum\test $test)
	{
		return parent::afterTestSetUp($test)->write(__FUNCTION__);
	}

	public function beforeTestMethod(atoum\test $test)
	{
		return parent::beforeTestMethod($test)->write(__FUNCTION__);
	}

	public function testAssertionSuccess(atoum\test $test)
	{
		return parent::testAssertionSuccess($test)->write(__FUNCTION__);
	}

	public function testAssertionFail(atoum\test $test)
	{
		return parent::testAssertionFail($test)->write(__FUNCTION__);
	}

	public function testError(atoum\test $test)
	{
		return parent::testError($test)->write(__FUNCTION__);
	}

	public function testException(atoum\test $test)
	{
		return parent::testException($test)->write(__FUNCTION__);
	}

	public function afterTestMethod(atoum\test $test)
	{
		return parent::afterTestMethod($test)->write(__FUNCTION__);
	}

	public function testRunStop(atoum\test $test)
	{
		return parent::testRunStop($test)->write(__FUNCTION__);
	}

	public function beforeTestTearDown(atoum\test $test)
	{
		return parent::beforeTestTearDown($test)->write(__FUNCTION__);
	}

	public function afterTestTearDown(atoum\test $test)
	{
		return parent::afterTestTearDown($test)->write(__FUNCTION__);
	}

	public function addWriter(report\writers\realtime $writer)
	{
		return $this->doAddWriter($writer);
	}

	public function runnerStop(atoum\runner $runner)
	{
		return parent::runnerStop($runner)->write(__FUNCTION__);
	}

	protected function write($event)
	{
		foreach ($this->writers as $writer)
		{
			$writer->writeRealtimeReport($this, $event);
		}

		return $this;
	}
}

?>
