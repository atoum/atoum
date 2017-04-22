<?php

namespace mageekguy\atoum\report\fields\test\travis;

use mageekguy\atoum;
use mageekguy\atoum\report\fields;
use mageekguy\atoum\test;

class start extends fields\test\travis
{
    private $event;
    private $observable;

    public function __construct()
    {
        parent::__construct([test::runStart]);
    }

    public function __toString()
    {
        $slug = self::slug(get_class($this->observable));

        return 'travis_fold:start:' . $slug . PHP_EOL . 'travis_time:start:' . $slug . PHP_EOL;
    }

    public function handleEvent($event, atoum\observable $observable)
    {
        $this->event = $event;
        $this->observable = $observable;

        return parent::handleEvent($event, $observable);
    }
}
