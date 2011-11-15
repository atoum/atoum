<?php

namespace mageekguy\atoum\observers;

use
	mageekguy\atoum
;

interface test extends atoum\observer
{
	public function testRunStart(atoum\test $test);
	public function beforeTestSetUp(atoum\test $test);
	public function afterTestSetUp(atoum\test $test);
	public function beforeTestMethod(atoum\test $test);
	public function testAssertionFail(atoum\test $test);
	public function testError(atoum\test $test);
	public function testException(atoum\test $test);
	public function testUncompleted(atoum\test $test);
	public function testAssertionSuccess(atoum\test $test);
	public function afterTestMethod(atoum\test $test);
	public function beforeTestTearDown(atoum\test $test);
	public function afterTestTearDown(atoum\test $test);
	public function testRunStop(atoum\test $test);
}

?>
