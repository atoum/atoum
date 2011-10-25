<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\asserter,
	mageekguy\atoum\exceptions
;

/**
 * @method  mageekguy\atoum\asserters\adapter               adapter()
 * @method  mageekguy\atoum\asserters\afterDestructionOf    afterDestructionOf()
 * @method  mageekguy\atoum\asserters\boolean               boolean()
 * @method  mageekguy\atoum\asserters\castToString          castToString()
 * @method  mageekguy\atoum\asserters\dateTime              dateTime()
 * @method  mageekguy\atoum\asserters\error                 error()
 * @method  mageekguy\atoum\asserters\exception             exception()
 * @method  mageekguy\atoum\asserters\float                 float()
 * @method  mageekguy\atoum\asserters\hash                  hash()
 * @method  mageekguy\atoum\asserters\integer               integer()
 * @method  mageekguy\atoum\asserters\mock                  mock()
 * @method  mageekguy\atoum\asserters\mysqlDateTime         mysqlDateTime()
 * @method  mageekguy\atoum\asserters\object                object()
 * @method  mageekguy\atoum\asserters\output                output()
 * @method  mageekguy\atoum\asserters\phpArray              phpArray()
 * @method  mageekguy\atoum\asserters\phpClass              phpClass()
 * @method  mageekguy\atoum\asserters\sizeOf                sizeOf()
 * @method  mageekguy\atoum\asserters\stream                stream()
 * @method  mageekguy\atoum\asserters\string                string()
 * @method  mageekguy\atoum\asserters\testedClass           testedClass()
 * @method  mageekguy\atoum\asserters\variable              variable()
 */
class testedClass extends phpClass
{
	public function __construct(asserter\generator $generator)
	{
		parent::__construct($generator);

		parent::setWith($generator->getTest()->getTestedClassName());
	}

	public function setWith($class, $label = null)
	{
		throw new exceptions\logic\badMethodCall('Unable to call method ' . __METHOD__ . '()');
	}
}

?>
