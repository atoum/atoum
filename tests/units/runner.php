<?php

namespace mageekguy\atoum\tests\units;

require_once(__DIR__ . '/../../scripts/runner.php');

\mageekguy\atoum\autoloader::addDirectory(__NAMESPACE__, __DIR__ . '/classes');
\mageekguy\atoum\autoloader::addDirectory(__NAMESPACE__ . '\asserters', __DIR__ . '/asserters');

?>
