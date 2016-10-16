<?php

namespace mageekguy\atoum\tests\functionals\asserters;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../runner.php';

class generator extends atoum\tests\functionals\test\functional
{
	/**
	 * @php >= 7.0
	 */
	public function testUsage()
	{
		if (version_compare(PHP_VERSION, '7.0') >= 0) {
			$generator = eval(<<<'PHP'
return function() {
    for ($i=0; $i<3; $i++) {
        yield ($i+1);
    }

    return 42;
};
PHP
			);
		}

		$this
			->generator($generator())
				->yields->isEqualTo(1)
				->yields->isEqualTo(2)
				->yields->isEqualTo(3)
				->yields->isNull()
				->returns->isEqualTo(42)
			->generator($generator())
				->size->isEqualTo(3)
		;
	}
}
