<?php

namespace <tpl:testClassNamespace />;

use
	mageekguy\atoum,
	<tpl:fullyQualifiedTestClassName /> as testedClass
;

<tpl:requireRunner>require_once __DIR__ . '/<tpl:relativeRunnerPath />';</tpl:requireRunner>

class <tpl:testClassName /> extends atoum\test
{<tpl:testMethods><tpl:testMethod>
	public function test<tpl:methodName/>()
	{
	}
</tpl:testMethod></tpl:testMethods>}
