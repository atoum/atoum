<?php

namespace mageekguy\atoum;

if (defined(__NAMESPACE__ . '\running') === false)
{
    define(__NAMESPACE__ . '\running',  true);
    define(__NAMESPACE__ . '\version', preg_replace('/\$Rev: ([^ ]+) \$/', '$1', '$Rev: DEVELOPMENT $'));
    define(__NAMESPACE__ . '\author', 'Frédéric Hardy');
    define(__NAMESPACE__ . '\mail', 'support@atoum.org');
    define(__NAMESPACE__ . '\repository',  'https://github.com/mageekguy/atoum');

	 if (\phar::running() === true)
	 {
		 define(__NAMESPACE__ . '\directory', rtrim(__DIR__, '/') . '/');
	 }
	 else
	 {
		 define(__NAMESPACE__ . '\directory', rtrim(__DIR__, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR);
	 }
}
