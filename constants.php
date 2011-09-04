<?php

namespace mageekguy\atoum;

defined($constant = __NAMESPACE__ . '\directory') || define($constant, ($pharPath = \phar::running(false)) ? 'phar://' . $pharPath : realpath(__DIR__));
defined($constant = __NAMESPACE__ . '\version') || define($constant, preg_replace('/\$Rev: ([^ ]+) \$/', '$1', '$Rev: DEVELOPMENT $'));
defined($constant = __NAMESPACE__ . '\author') || define($constant, 'Frédéric Hardy');
defined($constant = __NAMESPACE__ . '\mail') || define($constant, 'support@atoum.org');
defined($constant = __NAMESPACE__ . '\repository') || define($constant,  'https://github.com/mageekguy/atoum');

?>
