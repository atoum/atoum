<?php

namespace mageekguy\atoum\scripts;

require_once __DIR__ . '/../../constants.php';

use
	mageekguy\atoum,
	mageekguy\atoum\cli,
	mageekguy\atoum\php,
	mageekguy\atoum\writers,
	mageekguy\atoum\exceptions
;

class coverage extends runner
{
	const defaultReportFormat = 'xml';

	protected $reportOutputPath;
	protected $reportFormat;

	public function __construct($name, atoum\adapter $adapter = null)
	{
		parent::__construct($name, $adapter);

		$this->setReportFormat();
	}

	protected function doRun()
	{
		if (sizeof($this->getReports()) === 0)
		{
			$this->addDefaultReport();
		}

		switch ($this->reportFormat)
		{
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

	public function setReportFormat($format = null)
	{
		$this->reportFormat = $format ?: self::defaultReportFormat;

		return $this;
	}

	public function getReportFormat()
	{
		return $this->reportFormat;
	}

	public function setReportOutputPath($path)
	{
		$this->reportOutputPath = $path;

		return $this;
	}

	protected function reportOutputPathIsSet()
	{
		if ($this->reportOutputPath === null)
		{
			throw new exceptions\runtime('Coverage report output path is not set');
		}

		return $this;
	}

	protected function setArgumentHandlers()
	{
		return parent::setArgumentHandlers()
			->addArgumentHandler(
					function($script, $argument, $values) {
						if (sizeof($values) === 0)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
						}

						$script->setReportFormat(current($values));
					},
					array('-fmt', '--format'),
					'<xml|clover|html|treemap>',
					$this->locale->_('Coverage report format')
				)
			->addArgumentHandler(
					function($script, $argument, $values) {
						if (sizeof($values) === 0)
						{
							throw new exceptions\logic\invalidArgument(sprintf($script->getLocale()->_('Bad usage of %s, do php %s --help for more informations'), $argument, $script->getName()));
						}

						$script->setReportOutputPath(current($values));
					},
					array('-o', '--output'),
					'<path/to/file/or/directory>',
					$this->locale->_('Coverage report output path')
				)
		;
	}
}
