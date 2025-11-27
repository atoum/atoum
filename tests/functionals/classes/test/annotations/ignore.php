<?php

namespace atoum\atoum\tests\functionals\test\annotations;

use atoum\atoum;
use atoum\atoum\attributes as Attributes;

require_once __DIR__ . '/../../../runner.php';

#[Attributes\Tags('issue', 'issue-684')]
class ignore extends atoum\tests\functionals\test\functional
{
    #[Attributes\Ignore]
    public function testShouldBeIgnoredWithOnlyIgnoreAnnotation()
    {
        throw new atoum\exceptions\runtime('This test should be ignored');
    }

    #[Attributes\Ignore]
    public function testShouldBeIgnoredWithCommentStartingWithIgnoreWord()
    {
        throw new atoum\exceptions\runtime('This test should be ignored');
    }

    #[Attributes\Ignore]
    public function testShouldAlsoBeIgnored()
    {
        throw new atoum\exceptions\runtime('This test should be ignored');
    }

    /**
     * ignore
     * Alone, the "ignore" world should not mark the test as ignored
     */
    public function testShouldNotBeIgnored()
    {
        $this->string(uniqid());
    }
}
