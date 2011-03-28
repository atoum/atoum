<?php

namespace mageekguy\atoum;

define(__NAMESPACE__ . '\directory', \phar::running(false) ?: realpath(__DIR__));
define(__NAMESPACE__ . '\version', preg_replace('/\$Rev: (\d+) \$/', '$1', '$Rev: 325 $'));
define(__NAMESPACE__ . '\author', 'Frédéric Hardy');
define(__NAMESPACE__ . '\mail', 'support@atoum.org');
define(__NAMESPACE__ . '\repository',  'https://svn.mageekbox.net/repositories/unit/trunk');

?>
