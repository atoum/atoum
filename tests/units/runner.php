<?php

namespace mageekguy\atoum\tests\units;

require_once __DIR__ . '/../../scripts/runner.php';

\mageekguy\atoum\autoloader::get()
	->addDirectory(__NAMESPACE__, __DIR__ . '/classes')
	->addDirectory(__NAMESPACE__ . '\asserters', __DIR__ . '/asserters')
;
