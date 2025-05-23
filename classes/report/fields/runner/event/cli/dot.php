<?php

namespace atoum\atoum\report\fields\runner\event\cli;

use atoum\atoum\cli\progressBar;
use atoum\atoum\report;
use atoum\atoum\runner;
use atoum\atoum\test;

class dot extends report\fields\runner\event
{
    protected $count = 0;
    protected $progressBar;

    public function __construct(?progressBar\dot $progressBar = null)
    {
        parent::__construct();

        $this->progressBar = $progressBar ?: new progressBar\dot();
    }

    public function __toString()
    {
        $string = '';

        if ($this->observable !== null) {
            if ($this->event === runner::runStop) {
                $string = PHP_EOL;
            } else {
                switch ($this->event) {
                    case runner::runStart:
                        $this->progressBar->reset()->setIterations($this->observable->getTestMethodNumber());
                        break;

                    case test::success:
                        $this->progressBar->refresh('.');
                        break;

                    case test::fail:
                        $this->progressBar->refresh('F');
                        break;

                    case test::void:
                        $this->progressBar->refresh('0');
                        break;

                    case test::error:
                        $this->progressBar->refresh('E');
                        break;

                    case test::exception:
                        $this->progressBar->refresh('X');
                        break;

                    case test::uncompleted:
                        $this->progressBar->refresh('U');
                        break;

                    case test::skipped:
                        $this->progressBar->refresh('-');
                        break;
                }

                $string = (string) $this->progressBar;
            }
        }

        return $string;
    }

    public function getProgressBar()
    {
        return $this->progressBar;
    }
}
