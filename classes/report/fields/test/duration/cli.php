<?php

namespace mageekguy\atoum\report\fields\test\duration;

use mageekguy\atoum\cli\colorizer;
use mageekguy\atoum\cli\prompt;
use mageekguy\atoum\report;

class cli extends report\fields\test\duration
{
    protected $prompt = null;
    protected $titleColorizer = null;
    protected $durationColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setPrompt()
            ->setTitleColorizer()
            ->setDurationColorizer()
        ;
    }

    public function __toString()
    {
        return $this->prompt .
            sprintf(
                $this->locale->_('%1$s: %2$s.'),
                $this->titleColorizer->colorize($this->locale->_('Test duration')),
                $this->durationColorizer->colorize(
                    $this->value === null
                    ?
                    $this->locale->_('unknown')
                    :
                    sprintf(
                        $this->locale->__('%4.2f second', '%4.2f seconds', $this->value),
                        $this->value
                    )
                )
            ) .
            PHP_EOL
        ;
    }

    public function setPrompt(prompt $prompt = null)
    {
        $this->prompt = $prompt ?: new prompt();

        return $this;
    }

    public function getPrompt()
    {
        return $this->prompt;
    }

    public function setTitleColorizer(colorizer $colorizer = null)
    {
        $this->titleColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getTitleColorizer()
    {
        return $this->titleColorizer;
    }

    public function setDurationColorizer(colorizer $colorizer = null)
    {
        $this->durationColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getDurationColorizer()
    {
        return $this->durationColorizer;
    }
}
