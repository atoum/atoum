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

class santa extends cli
{
	const snowflake = '❅';
	const refreshDelay = 0.15;

	protected $sprite;
	protected $snowColorizer;
	protected $santa = '';
	protected $utime = 0;

	public function __construct(progressBar $progressBar = null)
	{
		parent::__construct($progressBar);

		$this->snowColorizer = new colorizer(38);

	$red = new colorizer(31);
	$black = new colorizer('1;30');
	$brown = new colorizer('38;5;94');

	$this->sprite = array(
	'            _            ',
	'           {_}           ',
	$red->colorize('           / \\           '),
	$red->colorize('          /   \\          '),
	$red->colorize('         /_____\\         '),
	'       {`_______`}    ',
	'        // . . \\\\        ',
	'       (/(__7__)\\)       ',
	'       |\'-` = `-\'|       ',
	'       |         |   ',
	$red->colorize('       /') . '\\       /' . $red->colorize('\\       '),
	$red->colorize('      /  ') . '\'.   .\'' . $red->colorize('  \\      '),
	$red->colorize('     /_/') . '   `"`   ' . $red->colorize('\\_\\     '),
	'    {__}' . $brown->colorize('###') . $black->colorize('[_]') . $brown->colorize('###') . '{__}    ',
	'    (_/' . $red ->colorize('\\_________/') . '\\_)	',
	$red->colorize('        |___|___|        '),
	$red->colorize('         |--|--|         '),
	$brown->colorize('        (__)') . '`' . $brown->colorize('(__)        ')
	);
	}

	protected function clear()
	{
		if (empty($this->santa) === false)
		{
			$lines = explode(PHP_EOL, $this->santa);
			return "\x1b[" . (sizeof($lines) - 1) . "F";
		}

	return '';
	}

	public function __toString()
	{
		if (microtime(true) - $this->utime < self::refreshDelay) {
			return '';
		}

		$string = PHP_EOL;

		if ($this->event !== runner::runStop)
		{
			$string .= $this->clear();

			for ($row = 0, $count = sizeof($this->sprite); $row < $count; $row++)
			{
				$string .= $this->getSnow() . $this->sprite[$row] . $this->getSnow() . PHP_EOL;
			}

			$string .= PHP_EOL;

			$this->santa = $string;
		}

		$this->utime = microtime(true);

		return $string;
	}

	protected function getSnow()
	{
		$string = '';

		for ($i = 0; $i < 10; $i++)
		{
			if (rand(0, 42) < rand(40, 42))
			{
				$string .= ' ';
			}
			else
			{
				$string .= $this->snowColorizer->colorize(self::snowflake);
			}
		}

		return $string;
	}
}
