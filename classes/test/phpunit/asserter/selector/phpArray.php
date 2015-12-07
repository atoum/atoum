<?php
namespace mageekguy\atoum\test\phpunit\asserter\selector;

use mageekguy\atoum;
use mageekguy\atoum\test\phpunit\asserter\selector;

class phpArray extends selector
{
	protected function selectAsserter($value)
	{
        if ($value instanceof \arrayAccess)
        {
            return new atoum\asserters\arrayAccess();
        }

        return new atoum\asserters\phpArray();
	}
} 