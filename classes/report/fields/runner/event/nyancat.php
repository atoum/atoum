<?php

namespace mageekguy\atoum\report\fields\runner\event;

use
	mageekguy\atoum\test,
	mageekguy\atoum\runner,
	mageekguy\atoum\report,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\cli\progressBar
;


class nyancat extends cli
{
	protected $cat = array(
		array(
			' ,---------,',
			'┓|       ^__^ ',
			'┗|     |｡◕‿‿◕｡| ',
			' ╰OO----OO  ',
		),
		array(
			' ,---------,',
			'┓|        ^__^',
			'┗|      |｡◕‿‿◕｡|',
			' ╰O-O----O-O',
		),
	);
	protected $offset = 0;
	protected $colors = array(31, 32, 33, 34, 35, 36);
	protected $events = array(test::success, test::fail, test::error, test::exception, test::void, test::uncompleted, test::skipped, runner::runStop);
	protected $nyan = '';

	public function __toString()
	{
		if (in_array($this->event, $this->events) === false || $this->event === runner::runStop)
		{
			return '';
		}

		$cat = $this->cat[$this->offset % 2];
		$this->nyan = "\x1b[" . count($cat) . "F";

		for ($row = 0; $row < count($cat); $row++)
		{
			for ($column = 0; $column < count($this->colors); $column++)
			{
				$this->nyan .= "\x1b[" . $this->getColor($column - $this->offset) . "m`·.,¸,.·*¯\x1b[0m";
			}

			$this->nyan .= "\x1b[34m" . $cat[$row] . PHP_EOL;
		}

		$this->offset = ($this->offset + 1) == count($this->colors) ? 0 : $this->offset + 1;

		return $this->nyan;
	}

	protected function getColor($offset)
	{
		$offset = $offset < 0
			? count($this->colors) - abs($offset)
			: $offset;

		return $this->colors[$offset];
	}
}
