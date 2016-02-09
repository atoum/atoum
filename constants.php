<?php

namespace mageekguy\atoum;

if (defined(__NAMESPACE__ . '\running') === false)
{
    define(__NAMESPACE__ . '\running',  true);
    define(__NAMESPACE__ . '\directory', __DIR__);
    define(__NAMESPACE__ . '\version', preg_replace('/\$Rev: ([^ ]+) \$/', '$1', '$Rev: 2.0.2 $'));
    define(__NAMESPACE__ . '\author', 'Frédéric Hardy');
    define(__NAMESPACE__ . '\mail', 'support@atoum.org');
    define(__NAMESPACE__ . '\repository',  'http://www.atoum.org/atoum');
}
