<?php

namespace mageekguy\atoum\report\fields\runner\event;

use
	mageekguy\atoum\test,
	mageekguy\atoum\runner,
	mageekguy\atoum\report,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\cli\progressBar,
	mageekguy\atoum\cli\colorizer
;

class nyancat extends cli
{
	protected $cat = array(
		array(
			'  ,------,  ',
			' ┓|      ^__^ ',
			' ┗|    |｡◕‿‿◕｡| ',
			'  ╰Oo---Oo  ',
		),
		array(
			'  ,--------,',
			' ┓|       ^__^',
			' ┗|     |｡◕‿‿◕｡|',
			'  ╰o-O---o-O',
		),
	);
	protected $curve = "`·.,¸,.·'¯";
	protected $offset = 0;
	protected $catColorizer;
	protected $rainbowColorizers;
	protected $nyan = '';
	protected $utime = 0;

	public function __construct(progressBar $progressBar = null)
	{
		parent::__construct($progressBar);

		$this->catColorizer = new colorizer(32);

		$this->rainbowColorizers = array(
			new colorizer(31),
			new colorizer(32),
			new colorizer(33),
			new colorizer(34),
			new colorizer(35),
			new colorizer(36)
		);
	}

	protected function clear()
	{
		if (empty($this->nyan) === false)
		{
			$lines = explode(PHP_EOL, $this->nyan);
			return "\x1b[" . (sizeof($lines) - 1) . "F";
		}
	}

	public function __toString()
	{
		if (in_array($this->event, array(test::fail, test::error, test::exception, test::uncompleted)))
		{
			$this->catColorizer = new colorizer(31);
		}

		if (microtime(true) - $this->utime < 0.15) {
			return '';
		}

		$string = PHP_EOL;

		if ($this->event !== runner::runStop)
		{
			$cat = $this->cat[$this->offset % 2];

			$string .= $this->clear();

			for ($row = 0, $count = sizeof($cat); $row < $count; $row++)
			{
				$string .= $this->getRainbowRow() . $this->catColorizer->colorize($cat[$row]) . PHP_EOL;
			}

			$string .= PHP_EOL;

			$this->offset = $this->offset + 1 === sizeof($this->rainbowColorizers)
				? 0
				: $this->offset + 1;

			$this->nyan = $string;
		}

		$this->utime = microtime(true);

		return $string;
	}

	protected function getRainbowRow()
	{
		$string = '';

		for ($column = 0, $count = sizeof($this->rainbowColorizers); $column < $count; $column++)
		{
			$string .= $this->getColorizer($column - $this->offset)->colorize($this->curve);
		}

		return $string;
	}

	protected function getColorizer($offset)
	{
		$offset = $offset < 0
			? sizeof($this->rainbowColorizers) - abs($offset)
			: $offset;

		return $this->rainbowColorizers[$offset];
	}
}
