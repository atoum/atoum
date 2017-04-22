<?php

namespace mageekguy\atoum\report\fields\test\travis;

use mageekguy\atoum;
use mageekguy\atoum\report\fields;
use mageekguy\atoum\test;

class stop extends fields\test\travis
{
    private $event;
    private $observable;

    public function __construct()
    {
        parent::__construct([test::runStop]);
    }

    public function __toString()
    {
        $slug = self::slug(get_class($this->observable));
        $duration = self::time($this->observable->getScore()->getTotalDuration());

        return 'travis_time:end:' . $slug . ':duration=' . $duration . PHP_EOL . 'travis_fold:end:' . $slug . PHP_EOL;
    }

    public function handleEvent($event, atoum\observable $observable)
    {
        $this->event = $event;
        $this->observable = $observable;

        return parent::handleEvent($event, $observable);
    }
}
