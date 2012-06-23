<?php

namespace mageekguy\atoum\test\engines;

use
	mageekguy\atoum,
	mageekguy\atoum\test
;

class inline extends test\engine
{
	protected $score = null;

	public function isAsynchronous()
	{
		return false;
	}

	public function __construct(atoum\factory $factory = null)
	{
		parent::__construct($factory);

		$this->score = $this->factory['mageekguy\atoum\score']();
	}

	public function run(atoum\test $test)
	{
		$currentTestMethod = $test->getCurrentMethod();

		if ($currentTestMethod !== null)
		{
			$testScore = $test->getScore();

			$test
				->setScore($this->score->reset())
				->runTestMethod($test->getCurrentMethod())
				->setScore($testScore)
			;
		}

		return $this;
	}

	public function getScore()
	{
		return $this->score;
	}
}
