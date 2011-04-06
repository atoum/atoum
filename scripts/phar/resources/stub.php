#!/usr/bin/env php
<?php

namespace mageekguy\atoum\phar;

use \mageekguy\atoum\scripts\phar;

define('mageekguy\atoum\scripts\runner\class', '\mageekguy\atoum\scripts\phar\stub');

require_once('phar://' . __FILE__ . '/scripts/runner.php');

__HALT_COMPILER();
