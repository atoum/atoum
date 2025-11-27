<?php

namespace atoum\atoum;

abstract class script
{
    public const padding = '   ';

    protected string $name = '';
    protected ?locale $locale = null;
    protected ?adapter $adapter = null;
    protected ?script\prompt $prompt = null;
    protected ?cli $cli = null;
    protected int $verbosityLevel = 0;
    protected ?writer $outputWriter = null;
    protected ?writer $infoWriter = null;
    protected ?writer $warningWriter = null;
    protected ?writer $errorWriter = null;
    protected ?writer $helpWriter = null;
    protected ?script\arguments\parser $argumentsParser = null;

    private bool $doRun = true;
    private array $help = [];

    public function __construct($name, ?adapter $adapter = null)
    {
        $this->name = (string) $name;

        $this
            ->setCli()
            ->setAdapter($adapter)
            ->setLocale()
            ->setPrompt()
            ->setArgumentsParser()
            ->setOutputWriter()
            ->setInfoWriter()
            ->setErrorWriter()
            ->setWarningWriter()
            ->setHelpWriter()
        ;

        if ($this->adapter->php_sapi_name() !== 'cli') {
            throw new exceptions\logic('\'' . $this->getName() . '\' must be used in CLI only');
        }
    }

    public function getDirectory(): string
    {
        $directory = $this->adapter->getcwd();

        return rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function setAdapter(?adapter $adapter = null): static
    {
        $this->adapter = $adapter ?: new adapter();

        return $this;
    }

    public function getAdapter(): adapter
    {
        return $this->adapter;
    }

    public function setLocale(?locale $locale = null): static
    {
        $this->locale = $locale ?: new locale();

        return $this;
    }

    public function getLocale(): locale
    {
        return $this->locale;
    }

    public function setArgumentsParser(?script\arguments\parser $parser = null): static
    {
        $this->argumentsParser = $parser ?: new script\arguments\parser();

        $this->setArgumentHandlers();

        return $this;
    }

    public function getArgumentsParser(): script\arguments\parser
    {
        return $this->argumentsParser;
    }

    public function setCli(?cli $cli = null): static
    {
        $this->cli = $cli ?: new cli();

        return $this;
    }

    public function getCli(): cli
    {
        return $this->cli;
    }

    public function hasArguments(): bool
    {
        return (count($this->argumentsParser->getValues()) > 0);
    }

    public function setOutputWriter(?writer $writer = null): static
    {
        $this->outputWriter = $writer ?: new writers\std\out($this->cli);

        return $this;
    }

    public function getOutputWriter(): writer
    {
        return $this->outputWriter;
    }

    public function setInfoWriter(?writer $writer = null): static
    {
        if ($writer === null) {
            $writer = new writers\std\out($this->cli);
            $writer
                ->addDecorator(new writer\decorators\rtrim())
                ->addDecorator(new writer\decorators\eol())
                ->addDecorator(new cli\clear())
            ;
        }

        $this->infoWriter = $writer;

        return $this;
    }

    public function getInfoWriter(): writer
    {
        return $this->infoWriter;
    }

    public function setWarningWriter(?writer $writer = null): static
    {
        if ($writer === null) {
            $writer = new writers\std\err($this->cli);
            $writer
                ->addDecorator(new writer\decorators\trim())
                ->addDecorator(new writer\decorators\prompt($this->locale->_('Warning: ')))
                ->addDecorator(new writer\decorators\eol())
                ->addDecorator(new cli\clear())
            ;
        }

        $this->warningWriter = $writer;

        return $this;
    }

    public function getWarningWriter(): writer
    {
        return $this->warningWriter;
    }

    public function setErrorWriter(?writer $writer = null): static
    {
        if ($writer === null) {
            $writer = new writers\std\err($this->cli);
            $writer
                ->addDecorator(new writer\decorators\trim())
                ->addDecorator(new writer\decorators\prompt($this->locale->_('Error: ')))
                ->addDecorator(new writer\decorators\eol())
                ->addDecorator(new cli\clear())
            ;
        }

        $this->errorWriter = $writer;

        return $this;
    }

    public function getErrorWriter(): writer
    {
        return $this->errorWriter;
    }

    public function setHelpWriter(?writer $writer = null): static
    {
        if ($writer === null) {
            $labelColorizer = new cli\colorizer('0;32');
            $labelColorizer->setPattern('/(^[^:]+: )/');

            $argumentColorizer = new cli\colorizer('0;37');
            $argumentColorizer->setPattern('/((?:^| )[-+]+[-a-z]+)/');

            $valueColorizer = new cli\colorizer('0;36');
            $valueColorizer->setPattern('/(<[^>]+>(?:\.\.\.)?)/');

            $writer = new writers\std\out();
            $writer
                ->addDecorator($labelColorizer)
                ->addDecorator($valueColorizer)
                ->addDecorator($argumentColorizer)
                ->addDecorator(new writer\decorators\rtrim())
                ->addDecorator(new writer\decorators\eol())
                ->addDecorator(new cli\clear())
            ;
        }

        $this->helpWriter = $writer;

        return $this;
    }

    public function getHelpWriter(): writer
    {
        return $this->helpWriter;
    }

    public function setPrompt(?script\prompt $prompt = null): static
    {
        if ($prompt === null) {
            $prompt = new script\prompt();
        }

        $this->prompt = $prompt->setOutputWriter($this->outputWriter);

        return $this;
    }

    public function getPrompt(): script\prompt
    {
        return $this->prompt;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHelp(): array
    {
        return $this->help;
    }

    public function help(): static
    {
        return $this
            ->writeHelpUsage()
            ->writeHelpOptions()
            ->stopRun()
        ;
    }

    public function addArgumentHandler(\Closure $handler, array $arguments, mixed $values = null, mixed $help = null, int $priority = 0): static
    {
        if ($help !== null) {
            $this->help[] = [$arguments, $values, $help];
        }

        $this->argumentsParser->addHandler($handler, $arguments, $priority);

        return $this;
    }

    public function setDefaultArgumentHandler(\Closure $handler): static
    {
        $this->argumentsParser->setDefaultHandler($handler);

        return $this;
    }

    public function run(array $arguments = []): static
    {
        $this->adapter->ini_set('log_errors_max_len', 0);
        $this->adapter->ini_set('log_errors', 'Off');
        $this->adapter->ini_set('display_errors', 'stderr');

        $this->doRun = true;

        if ($this->parseArguments($arguments)->doRun === true) {
            $this->doRun();
        }

        return $this;
    }

    public function prompt(string $message): string
    {
        return trim($this->prompt->ask(rtrim($message)));
    }

    public function writeLabel(string $label, string $value, int $level = 0): static
    {
        static::writeLabelWithWriter($label, $value, $level, $this->helpWriter);

        return $this;
    }

    public function writeLabels(array $labels, int $level = 1): static
    {
        static::writeLabelsWithWriter($labels, $level, $this->helpWriter);

        return $this;
    }

    public function clearMessage(): static
    {
        $this->outputWriter->clear();

        return $this;
    }

    public function writeMessage(string $message): static
    {
        $this->outputWriter
            ->removeDecorators()
            ->write($message)
        ;

        return $this;
    }

    public function writeInfo(string $info): static
    {
        $this->infoWriter->write($info);

        return $this;
    }

    public function writeHelp(string $message): static
    {
        $this->helpWriter->write($message);

        return $this;
    }

    public function writeWarning(string $warning): static
    {
        $this->warningWriter->write($warning);

        return $this;
    }

    public function writeError(string $message): static
    {
        $this->errorWriter->write($message);

        return $this;
    }

    public function verbose(string $message, int $verbosityLevel = 1): static
    {
        if ($verbosityLevel > 0 && $this->verbosityLevel >= $verbosityLevel) {
            $this->writeInfo($message);
        }

        return $this;
    }

    public function increaseVerbosityLevel(): static
    {
        $this->verbosityLevel++;

        return $this;
    }

    public function decreaseVerbosityLevel(): static
    {
        if ($this->verbosityLevel > 0) {
            $this->verbosityLevel--;
        }

        return $this;
    }

    public function getVerbosityLevel(): int
    {
        return $this->verbosityLevel;
    }

    public function resetVerbosityLevel(): static
    {
        $this->verbosityLevel = 0;

        return $this;
    }

    protected function setArgumentHandlers(): static
    {
        $this->argumentsParser->resetHandlers();

        $this->help = [];

        return $this;
    }

    protected function canRun(): bool
    {
        return ($this->doRun === true);
    }

    protected function stopRun(): static
    {
        $this->doRun = false;

        return $this;
    }

    protected function writeHelpUsage(): static
    {
        if ($this->help) {
            $this->writeHelp($this->locale->_('Usage: %s [options]', $this->getName()));
        }

        return $this;
    }

    protected function writeHelpOptions(): static
    {
        if ($this->help) {
            $arguments = [];

            foreach ($this->help as $help) {
                if ($help[1] !== null) {
                    foreach ($help[0] as & $argument) {
                        $argument .= ' ' . $help[1];
                    }
                }

                $arguments[implode(', ', $help[0])] = $help[2];
            }

            $this->writeHelp($this->locale->_('Available options are:'));

            static::writeLabelsWithWriter($arguments, 1, $this->helpWriter);
        }

        return $this;
    }

    protected function parseArguments(array $arguments): static
    {
        $this->argumentsParser->parse($this, $arguments);

        return $this;
    }

    protected function doRun(): static
    {
        return $this;
    }

    protected static function writeLabelWithWriter(string $label, string $value, int $level, writer $writer): writer
    {
        return $writer->write('  ' . $label . '  ' . trim($value));
    }

    protected static function writeLabelsWithWriter(array $labels, int $level, writer $writer): writer
    {
        $maxLength = 0;

        foreach (array_keys($labels) as $label) {
            $length = strlen($label);

            if ($length > $maxLength) {
                $maxLength = $length;
            }
        }

        foreach ($labels as $label => $value) {
            $value = explode("\n", trim($value));

            static::writeLabelWithWriter(str_pad($label, $maxLength, ' ', STR_PAD_RIGHT), $value[0], $level, $writer);

            if (count($value) > 1) {
                foreach (array_slice($value, 1) as $line) {
                    static::writeLabelWithWriter(str_repeat(' ', $maxLength), $line, $level, $writer);
                }
            }
        }

        return $writer;
    }
}
