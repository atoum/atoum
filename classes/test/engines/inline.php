<?php

namespace atoum\atoum\test\engines;

use atoum\atoum;
use atoum\atoum\test;

class inline extends test\engine
{
    protected ?atoum\test\score $score = null;

    public function isAsynchronous(): bool
    {
        return false;
    }

    public function __construct(?atoum\test\score $score = null)
    {
        $this->setScore();
    }

    public function setScore(?atoum\test\score $score = null): static
    {
        $this->score = $score ?: new atoum\test\score();

        return $this;
    }

    public function getScore(): ?atoum\score
    {
        return $this->score;
    }

    public function run(atoum\test $test): static
    {
        $currentTestMethod = $test->getCurrentMethod();

        if ($currentTestMethod !== null) {
            $testScore = $test->getScore();

            $test
                ->setScore($this->score->reset())
                ->runTestMethod($currentTestMethod)
                ->setScore($testScore)
            ;
        }

        return $this;
    }
}
