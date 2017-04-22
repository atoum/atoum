<?php

namespace mageekguy\atoum\report\field\decorators\travis;

use mageekguy\atoum\report\field;
use mageekguy\atoum\report\field\decorator;

class fold extends decorator
{
    private $slug;

    public function __construct(field $field, $slug)
    {
        parent::__construct($field);

        $this->slug = $slug;
    }

    public function decorate($string)
    {
        if ($string == '') {
            return (string) $string;
        }

        $newlinePosition = strpos($string, PHP_EOL);

        if (strpos($string, PHP_EOL, $newlinePosition + 1) === false) {
            return $string;
        }

        return 'travis_fold:start:' . $this->slug . PHP_EOL .
            $string .
            'travis_fold:end:' . $this->slug . PHP_EOL;
    }
}
