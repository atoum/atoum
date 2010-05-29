<?php

namespace mageekguy\tests\unit;

class score
{
	protected $failNumber = 0;
	protected $passNumber = 0;
	protected $assertions = array();

	public function addPass($file, $line, $class, $method, $asserter)
	{
		$this->passNumber++;

		$this->assertions[$class][$method][] = array(
			'file' => $file,
			'line' => $line,
			'asserter' => $asserter,
			'fail' => null
		);

		return $this;
	}

	public function addFail($file, $line, $class, $method, $asserter, $reason)
	{
		$this->failNumber++;

		$this->assertions[$class][$method][] = array(
			'file' => $file,
			'line' => $line,
			'asserter' => $asserter,
			'fail' => $reason
		);

		return $this;
	}
}

?>
