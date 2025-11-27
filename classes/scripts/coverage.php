<?php

namespace atoum\atoum\scripts;

require_once __DIR__ . '/../../constants.php';

use atoum\atoum;
use atoum\atoum\exceptions;

class coverage extends runner
{
    public const defaultReportFormat = 'xml';

    protected ?string $reportOutputPath = null;
    protected ?string $reportFormat = null;

    public function __construct(string $name, ?atoum\adapter $adapter = null)
    {
        parent::__construct($name, $adapter);

        $this->setReportFormat();
    }

    protected function doRun(): static
    {
        if (count($this->getReports()) === 0) {
            $this->addDefaultReport();
        }

        switch ($this->reportFormat) {
            case 'xml':
            case 'clover':
                $writer = new atoum\writers\file($this->reportOutputPathIsSet()->reportOutputPath);
                $report = new atoum\reports\asynchronous\clover();
                $this->addReport($report->addWriter($writer));
                break;

            case 'html':
                $field = new atoum\report\fields\runner\coverage\html('Code coverage', $this->reportOutputPathIsSet()->reportOutputPath);
                $field->setRootUrl('file://' . realpath(rtrim($this->reportOutputPathIsSet()->reportOutputPath, DIRECTORY_SEPARATOR)) . '/index.html');
                current($this->getReports())->addField($field);
                break;

            case 'treemap':
                $field = new atoum\report\fields\runner\coverage\treemap('Code coverage treemap', $this->reportOutputPathIsSet()->reportOutputPath);
                $field->setTreemapUrl('file://' . realpath(rtrim($this->reportOutputPathIsSet()->reportOutputPath, DIRECTORY_SEPARATOR)) . '/index.html');
                current($this->getReports())->addField($field);
                break;

            default:
                throw new exceptions\logic\invalidArgument('Invalid format for coverage report');
        }

        return parent::doRun();
    }

    public function setReportFormat(?string $format = null): static
    {
        $this->reportFormat = $format ?: self::defaultReportFormat;

        return $this;
    }

    public function getReportFormat(): string
    {
        return $this->reportFormat;
    }

    public function setReportOutputPath(string $path): static
    {
        $this->reportOutputPath = $path;

        return $this;
    }

    protected function reportOutputPathIsSet(): static
    {
        if ($this->reportOutputPath === null) {
            throw new exceptions\runtime('Coverage report output path is not set');
        }

        return $this;
    }

    protected function setArgumentHandlers(): static
    {
        return parent::setArgumentHandlers()
            ->addArgumentHandler(
                function ($script, $argument, $values) {
                    if (count($values) === 0) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setReportFormat(current($values));
                },
                ['-fmt', '--format'],
                '<xml|clover|html|treemap>',
                $this->locale->_('Coverage report format')
            )
            ->addArgumentHandler(
                function ($script, $argument, $values) {
                    if (count($values) === 0) {
                        throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
                    }

                    $script->setReportOutputPath(current($values));
                },
                ['-o', '--output'],
                '<path/to/file/or/directory>',
                $this->locale->_('Coverage report output path')
            )
        ;
    }
}
