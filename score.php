<?php

namespace mageekguy\tests\unit;

class score
{
	protected $failedAssertions = array();

	public function addFailedAssertion($file, $line, $class, $method, $asserter, $reason)
	{
		$this->failedAssertions[$class][] = array(
			'file' => $file,
			'line' => $line,
			'method' => $method,
			'asserter' => $asserter,
			'reason' => $reason
		);

		return $this;
	}
}

?>
