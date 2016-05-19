<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\tools,
	mageekguy\atoum\asserter
;

class output extends phpString
{
	public function __construct(asserter\generator $generator = null, tools\variable\analyzer $analyzer = null, atoum\locale $locale = null)
	{
		parent::__construct($generator, $analyzer, $locale);

		$this->setWith(null);
	}

	public function setWith($value = null, $charlist = null, $checkType = true)
	{
		if ($value instanceof \closure)
		{
			ob_start();
			$value($this->getTest());
			$value = ob_get_clean();
		}
		else if ($value === null && ob_get_level() > 0)
		{
			$value = ob_get_clean();
			ob_start();
		}

		return parent::setWith($value, $charlist, $checkType);
	}
}
