<?php

namespace mageekguy\atoum\tests\units\reports;

use \mageekguy\atoum;
use \mageekguy\atoum\report;
use \mageekguy\atoum\reports;

require_once(__DIR__ . '/../../runner.php');

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
		;
	}
}

?>
