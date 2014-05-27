<?php

namespace mageekguy\atoum\tools\diffs;

use
	mageekguy\atoum\tools,
	mageekguy\atoum\exceptions
;

class variable extends tools\diff
{
	protected $analyzer = null;

	public function __construct($expected = null, $actual = null)
	{
		$this->setAnalyzer();

		parent::__construct($expected, $actual);
	}

	public function setAnalyzer(tools\variable\analyzer $analyzer = null)
	{
		$this->analyzer = $analyzer ?: new tools\variable\analyzer();

		return $this;
	}

	public function getAnalyzer()
	{
		return $this->analyzer;
	}

	public function setExpected($mixed)
	{
		return parent::setExpected($this->analyzer->dump($mixed));
	}

	public function setActual($mixed)
	{
		return parent::setActual($this->analyzer->dump($mixed));
	}

	public function make($expected = null, $actual = null)
	{
		if ($expected !== null)
		{
			$this->setExpected($expected);
		}

		if ($expected !== null)
		{
			$this->setActual($actual);
		}

		if ($this->expected === null)
		{
			throw new exceptions\runtime('Expected is undefined');
		}

		if ($this->actual === null)
		{
			throw new exceptions\runtime('Actual is undefined');
		}

		return parent::make();
	}
}
