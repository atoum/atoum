<?php

namespace mageekguy\atoum\tests\units\tools;

use
    mageekguy\atoum,
    mageekguy\atoum\tools
;

require_once __DIR__ . '/../../runner.php';

class glob extends atoum\test
{
    /**
    * @dataProvider regexProvider
    */
    public function testToRegex($glob, $match, $noMatch)
    {
        foreach ($match as $m) {
            $this->assert
                ->integer(preg_match(tools\glob::toRegex($glob), $m))
                    ->isEqualTo(1);
        }

        foreach ($noMatch as $m) {
            $this->assert
                ->integer(preg_match(tools\glob::toRegex($glob), $m))
                    ->isZero();
        }
    }

    public function regexProvider()
    {
        return array(
            array('', array(''), array('f', '/')),
            array('*', array('foo'), array('foo/', '/foo')),
            array('foo.*', array('foo.php', 'foo.a', 'foo.'), array('fooo.php', 'foo.php/foo')),
            array('fo?', array('foo', 'fot'), array('fooo', 'ffoo', 'fo/')),
            array('fo{o,t}', array('foo', 'fot'), array('fob', 'fo/')),
            array('foo(bar|foo)', array('foo(bar|foo)'), array('foobar', 'foofoo')),
            array('foo,bar', array('foo,bar'), array('foo', 'bar')),
            array('fo{o,\\,}', array('foo', 'fo,'), array()),
            array('fo{o,\\\\}', array('foo', 'fo\\'), array()),
            array('/foo', array('/foo'), array('foo')),
        );
    }
}