<?php

namespace mageekguy\atoum\tests\functionals\test\annotations;

use mageekguy\atoum;

require_once __DIR__ . '/../../../runner.php';

/**
 * @tags issue issue-684
 */
class ignore extends atoum\tests\functionals\test\functional
{
    /**
     * @ignore
     */
    public function testShouldBeIgnoredWithOnlyIgnoreAnnotation()
    {
        throw new atoum\exceptions\runtime('This test should be ignored');
    }

    /**
     * @ignore
     * Ignore <- starting a comment with "Ignore" should not cause any problem
     */
    public function testShouldBeIgnoredWithCommentStartingWithIgnoreWord()
    {
        throw new atoum\exceptions\runtime('This test should be ignored');
    }

    /**
     * @ignore
     * Comment starting with anything else than "ignore" "Ignore" should not cause any problem
     */
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
