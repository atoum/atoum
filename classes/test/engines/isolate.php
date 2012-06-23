<?php

namespace mageekguy\atoum\test\engines;

use
	mageekguy\atoum,
	mageekguy\atoum\test\engines
;

class isolate extends engines\concurrent
{
	protected $score = null;

	public function __construct(atoum\factory $factory = null)
	{
		parent::__construct($factory);

		$this->score = $this->factory['mageekguy\atoum\score']();
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
