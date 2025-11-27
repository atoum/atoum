<?php

namespace atoum\atoum\test\engines;

use atoum\atoum;
use atoum\atoum\test\engines;

class isolate extends engines\concurrent
{
    protected ?atoum\score $score = null;

    public function __construct(?atoum\score $score = null)
    {
        parent::__construct();
        $this->setScore($score);
    }

    public function setScore(?atoum\score $score = null): static
    {
        $this->score = $score ?: new atoum\score();

        return $this;
    }

    public function run(atoum\test $test): static
    {
        parent::run($test);

        $this->score = parent::getScore();

        while ($this->score === null) {
            $this->score = parent::getScore();
        }

        return $this;
    }

    public function getScore(): ?atoum\score
    {
        return $this->score;
    }
}
