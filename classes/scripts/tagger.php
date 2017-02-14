<?php

namespace mageekguy\atoum\scripts;

use mageekguy\atoum;
use mageekguy\atoum\exceptions;
use mageekguy\atoum\scripts;

class tagger extends atoum\script
{
    protected $engine = null;

    public function __construct($name, atoum\adapter $adapter = null)
    {
        parent::__construct($name, $adapter);

        $this->setEngine();
    }

    public function setEngine(tagger\engine $engine = null)
    {
        $this->engine = $engine ?: new scripts\tagger\engine();

        $this->setArgumentHandlers();

        return $this;
    }

    public function getEngine()
    {
        return $this->engine;
    }

    protected function setArgumentHandlers()
    {
        if ($this->engine !== null) {
            $engine = $this->engine;

            $this
                ->addArgumentHandler(
                    function ($script, $argument, $values) {
                        if (count($values) != 0) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                        }

                        $script->help();
                    },
                    ['-h', '--help'],
                    null,
                    $this->locale->_('Display this help')
                )
                ->addArgumentHandler(
                    function ($script, $argument, $version) use ($engine) {
                        if (count($version) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                        }

                        $engine->setVersion(current($version));
                    },
                    ['-v', '--version'],
                    '<string>',
                    $this->locale->_('Use <string> as version value')
                )
                ->addArgumentHandler(
                    function ($script, $argument, $versionPattern) use ($engine) {
                        if (count($versionPattern) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                        }

                        $engine->setVersionPattern(current($versionPattern));
                    },
                    ['-vp', '--version-pattern'],
                    '<regex>',
                    $this->locale->_('Use <regex> to set version in source files')
                )
                ->addArgumentHandler(
                    function ($script, $argument, $directory) use ($engine) {
                        if (count($directory) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                        }

                        $engine->setSrcDirectory(current($directory));
                    },
                    ['-s', '--src-directory'],
                    '<directory>',
                    $this->locale->_('Use <directory> as source directory')
                )
                ->addArgumentHandler(
                    function ($script, $argument, $directory) use ($engine) {
                        if (count($directory) != 1) {
                            throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                        }

                        $engine->setDestinationDirectory(current($directory));
                    },
                    ['-d', '--destination-directory'],
                    '<directory>',
                     $this->locale->_('Save tagged files in <directory>')
                )
            ;
        }

        return $this;
    }

    protected function doRun()
    {
        $this->engine->tagVersion();

        return $this;
    }
}
