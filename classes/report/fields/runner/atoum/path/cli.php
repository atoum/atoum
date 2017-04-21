<?php

namespace mageekguy\atoum\report\fields\runner\atoum\path;

use mageekguy\atoum;
use mageekguy\atoum\cli\colorizer;
use mageekguy\atoum\cli\prompt;
use mageekguy\atoum\report;

class cli extends report\fields\runner\atoum\path
{
    protected $prompt = null;
    protected $titleColorizer = null;
    protected $pathColorizer = null;

    public function __construct()
    {
        parent::__construct();

        $this
            ->setPrompt()
            ->setTitleColorizer()
            ->setPathColorizer()
        ;
    }

    public function __toString()
    {
        return
            $this->prompt .
            sprintf(
                $this->locale->_('%1$s: %2$s'),
                $this->titleColorizer->colorize($this->locale->_('atoum path')),
                $this->pathColorizer->colorize($this->path)
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

    public function setPathColorizer(colorizer $colorizer = null)
    {
        $this->pathColorizer = $colorizer ?: new colorizer();

        return $this;
    }

    public function getPathColorizer()
    {
        return $this->pathColorizer;
    }
}
