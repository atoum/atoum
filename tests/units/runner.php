<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../scripts/runner.php';

atoum\autoloader::get()->addDirectory(__NAMESPACE__ . '\asserters', __DIR__ . '/asserters');
