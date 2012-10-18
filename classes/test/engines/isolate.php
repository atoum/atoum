<?php

namespace mageekguy\atoum\test\engines;

use
	mageekguy\atoum,
	mageekguy\atoum\test\engines
;

class isolate extends engines\concurrent
{
	protected $score = null;

	public function __construct(atoum\score $score = null)
	{
		parent::__construct($score);
	}

	public function setScore(atoum\score $score = null)
	{
		$this->score = $score ?: new atoum\score();

		return $this;
	}

	public function run(atoum\test $test)
	{
		parent::run($test);

		$this->score = parent::getScore();

		while ($this->score === null)
		{
			$this->score = parent::getScore();
		}

		return $this;
	}

	public function getScore()
	{
		return $this->score;
	}
}
