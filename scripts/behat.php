<?php

define('behatDirectory', __DIR__ . '/../tests/functionals');

chdir(behatDirectory);

require_once behatDirectory . '/behat.phar';
