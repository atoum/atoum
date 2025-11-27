<?php

namespace atoum\atoum\report\fields\test\travis;

use atoum\atoum;
use atoum\atoum\report\fields;
use atoum\atoum\test;

class start extends fields\test\travis
{
    private $event;
    private $observable;

    public function __construct()
    {
        parent::__construct([test::runStart]);
    }

    public function __toString(): string
    {
        $slug = self::slug(get_class($this->observable));

        return 'travis_fold:start:' . $slug . PHP_EOL . 'travis_time:start:' . $slug . PHP_EOL;
    }

    public function handleEvent(string $event, atoum\observable $observable): bool
    {
        $this->event = $event;
        $this->observable = $observable;

        return parent::handleEvent($event, $observable);
    }
}
