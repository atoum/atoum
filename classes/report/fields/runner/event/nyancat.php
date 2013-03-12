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
			'  ,-------,  ',
			' ┓|       ^__^ ',
			' ┗|     |｡◕‿‿◕｡| ',
			'  ╰OO----OO  ',
		),
		array(
			'  ,---------,',
			' ┓|        ^__^',
			' ┗|      |｡◕‿‿◕｡|',
			'  ╰O-O----O-O',
		),
	);
    protected $curve = "`·.,¸,.·'¯";
	protected $offset = 0;
    protected $catColorizer;
    protected $rainbowColorizers;
	protected $nyan = '';

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

    public function __toString()
	{
        $string = PHP_EOL;

        if ($this->event === test::fail)
        {
            $this->catColorizer = new colorizer(31);
        }

		if ($this->event !== runner::runStop)
		{
            $cat = $this->cat[$this->offset % 2];

            if (empty($this->nyan) === false)
            {
                $lines = explode(PHP_EOL, $this->nyan);
                $string .= "\x1b[" . (count($lines) - 1) . "F";
            }

            for ($row = 0; $row < count($cat); $row++)
            {
                $string .= $this->getRainbowRow() . $this->catColorizer->colorize($cat[$row]) . PHP_EOL;
            }

            $string .= PHP_EOL;

            $this->offset = $this->offset + 1 === count($this->rainbowColorizers)
                ? 0
                : $this->offset + 1;

            $this->nyan = $string;
        }

        return $string;
	}

    protected function getRainbowRow()
    {
        $string = '';

        for ($column = 0; $column < count($this->rainbowColorizers); $column++)
        {
            $string .= $this->getColorizer($column - $this->offset)->colorize($this->curve);
        }

        return $string;
    }

	protected function getColorizer($offset)
	{
		$offset = $offset < 0
			? count($this->rainbowColorizers) - abs($offset)
			: $offset;

		return $this->rainbowColorizers[$offset];
	}
}
