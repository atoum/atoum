<?php

namespace atoum\atoum\scripts;

use atoum\atoum;
use atoum\atoum\exceptions;
use atoum\atoum\iterators;

class compiler extends atoum\script
{
    protected bool $compile = true;
    protected ?string $srcDirectory = null;
    protected ?string $destinationDirectory = null;
    protected ?string $destinationFile = null;
    protected ?string $bootstrapFile = null;

    public function setSrcDirectory(string $directory): static
    {
        $this->srcDirectory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return $this;
    }

    public function getSrcDirectory(): ?string
    {
        return $this->srcDirectory;
    }

    public function setDestinationFile(string $file): static
    {
        $this->destinationFile = $file;

        return $this;
    }

    public function getDestinationDirectory(): ?string
    {
        return $this->destinationDirectory;
    }

    public function setBootstrapFile(string $bootstrapFile): static
    {
        $this->bootstrapFile = $bootstrapFile;

        return $this;
    }

    public function getBootstrapFile(): ?string
    {
        return $this->bootstrapFile;
    }

    protected function setArgumentHandlers(): static
    {
        return $this
            ->addArgumentHandler(
                function ($script, $argument, $values) {
                    if (count($values) !== 0) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->help();
                },
                ['-h', '--help'],
                null,
                'Display this help'
            )
            ->addArgumentHandler(
                function ($script, $argument, $values) {
                    if (count($values) !== 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setSrcDirectory($values[0]);
                },
                ['-sd', '--src-directory'],
                '<directory>',
                $this->locale->_('Source directory <dir>')
            )
            ->addArgumentHandler(
                function ($script, $argument, $values) {
                    if (count($values) !== 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setDestinationFile($values[0]);
                },
                ['-df', '--destination-file'],
                '<file>',
                $this->locale->_('Destination file <file>')
            )
            ->addArgumentHandler(
                function ($script, $argument, $values) {
                    if (count($values) !== 1) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setBootstrapFile($values[0]);
                },
                ['-bf', '--bootstrap-file'],
                '<file>',
                $this->locale->_('Bootstrap file <file>')
            )
        ;
    }

    protected function doRun(): static
    {
        $data = [];

        $srcDirectory = new atoum\fs\path($this->srcDirectory);

        foreach (new iterators\recursives\atoum\source($this->srcDirectory) as $file) {
            $file = new atoum\fs\path($file);

            $data[(string) $file->relativizeFrom($srcDirectory)] = file_get_contents($file);
        }

        $bootstrapFile = new atoum\fs\path($this->bootstrapFile);
        $bootstrapFile = $bootstrapFile->relativizeFrom($srcDirectory);

        $bootstrap  = '<?php $directory = sys_get_temp_dir() . \'/\' . basename(__FILE__);';
        $bootstrap .= '$bootstrap = $directory . \'/' . $bootstrapFile . '\';';
        $bootstrap .= 'if (is_file($bootstrap) === false || filemtime(__FILE__) > filemtime($bootstrap))';
        $bootstrap .= '{';
        $bootstrap .= '$data = eval(substr(file_get_contents(__FILE__), __COMPILER_HALT_OFFSET__));';
        $bootstrap .= 'foreach ($data as $file => $contents)';
        $bootstrap .= '{';
        $bootstrap .= '$file = $directory . \'/\' . $file;';
        $bootstrap .= '@mkdir(dirname($file), 0777, true);';
        $bootstrap .= '@file_put_contents($file, $contents);';
        $bootstrap .= '}';
        $bootstrap .= '}';
        $bootstrap .= 'require $bootstrap;';
        $bootstrap .= '__halt_compiler();';

        if (file_put_contents($this->destinationFile, $bootstrap . 'return ' . var_export($data, true) . ';') === false) {
            throw new exceptions\runtime($this->locale->_('Unable to write in file \'%s\'', $this->destinationFile));
        }

        return $this;
    }
}
