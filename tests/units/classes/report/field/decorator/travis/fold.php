<?php

namespace mageekguy\atoum\tests\units\report\field\decorators\travis;

use mageekguy\atoum;

require_once __DIR__ . '/../../../../../runner.php';

class fold extends atoum\test
{
    public function testClass()
    {
        $this->testedClass->extends(atoum\report\field\decorator::class);
    }

    public function testDecorate(atoum\report\field $field)
    {
        $this
            ->given($this->calling($field)->__toString->doesNothing)
            ->if($this->newTestedInstance($field, $slug = uniqid()))
            ->then
                ->castToString($this->testedInstance)->isEmpty
            ->if($this->calling($field)->__toString = $string = uniqid())
            ->then
                ->castToString($this->testedInstance)->isEqualTo($string)
            ->if($this->calling($field)->__toString = $string = (uniqid() . PHP_EOL . uniqid() . PHP_EOL))
            ->then
                ->castToString($this->testedInstance)->isEqualTo('travis_fold:start:' . $slug . PHP_EOL . $string . 'travis_fold:end:' . $slug . PHP_EOL)
        ;
    }
}
