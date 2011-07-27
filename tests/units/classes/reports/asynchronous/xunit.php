<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	ageekguy\atoum\asserter\exception,
	mageekguy\atoum\reports\asynchronous as reports
;

require_once(__DIR__ . '/../../../runner.php');

class xunit extends atoum\test
{
	public function test__construct()
	{
		$rep = new reports\xunit();

		$this->assert
			->array($rep->getRunnerFields(atoum\runner::runStart))->isEqualTo(array())
			->array($rep->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					new atoum\report\fields\runner\xunit()
				)
			)
			->array($rep->getTestFields(atoum\test::runStart))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::beforeSetUp))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::afterSetUp))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::beforeTestMethod))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::success))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::fail))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::error))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::exception))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::afterTestMethod))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::beforeTearDown))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::afterTearDown))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::runStop))->isEqualTo(array())
			->object($rep->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
		;

		$adapter = new atoum\test\adapter();
		$adapter->extension_loaded = function($extension) { return true; };

		$rep = new reports\xunit($adapter);

		$this->assert
			->array($rep->getRunnerFields(atoum\runner::runStart))->isEqualTo(array())
			->array($rep->getRunnerFields(atoum\runner::runStop))->isEqualTo(array(
					new atoum\report\fields\runner\xunit()
				)
			)
			->array($rep->getTestFields(atoum\test::runStart))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::beforeSetUp))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::afterSetUp))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::beforeTestMethod))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::success))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::fail))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::error))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::exception))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::afterTestMethod))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::beforeTearDown))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::afterTearDown))->isEqualTo(array())
			->array($rep->getTestFields(atoum\test::runStop))->isEqualTo(array())
			->object($rep->getAdapter())->isIdenticalTo($adapter)
			->adapter($adapter)->call('extension_loaded')->withArguments('libxml')->once()
		;

		$adapter->extension_loaded = function($extension) { return false; };

		$this->assert
			->exception(function() use ($adapter) {
							$rep = new reports\xunit($adapter);
						}
					)
			->isInstanceOf('mageekguy\atoum\exceptions\runtime')
			->hasMessage('libxml PHP extension is mandatory for xunit report')
			;
	}

	public function testSetAdapter()
	{
		$rep = new reports\xunit();

		$this->assert
			->object($rep->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($rep)
			->object($rep->getAdapter())->isIdenticalTo($adapter)
		;
	}
}

?>
