<?php

namespace mageekguy\atoum\runner\visitors;

use
	mageekguy\atoum\runner,
	mageekguy\atoum\cli\table,
	mageekguy\atoum\runner\visitor
;

class verbose implements visitor
{
	private $table;
	private $verbosity;

	public function __construct($verbosity = null, table $table = null)
	{
		$this->table = $table ?: new table(array('Runner property', 'value'));
		$this->verbosity = (int) $verbosity;
	}

	public function __toString()
	{
		return (string) $this->table;
	}

	public function visit(runner $runner)
	{
		$this->table
			->addRow(array('Runner class', get_class($runner)))
			->addRow(array('Autoloader file', $runner->getAutoloaderFile() === null ? 'N/A' : $runner->getAutoloaderFile()))
			->addRow(array('Bootstrap file', $runner->getBootstrapFile() === null ? 'N/A' : $runner->getBootstrapFile()))
			->addRow(array('Max children processes', $runner->getMaxChildrenNumber() === null ? 'N/A' : $runner->getMaxChildrenNumber()))
			->addRow(array('Reports', sizeof($runner->getReports()) === 0 ? 'N/A' : array_map(function($report) { return get_class($report); }, $runner->getReports())))
			->addRow(array('xDebug configuration', $runner->getXdebugConfig() === null ? 'N/A' : $runner->getXdebugConfig()))
			->addRow(array('Accepted file extensions', join(', ', $runner->getTestDirectoryIterator()->getAcceptedExtensions())))
			->addRow(array('Test files', sizeof($runner->getTestPaths()) === 0 ? '0' : ($this->verbosity < 2 ? sizeof($runner->getTestPaths()) : $runner->getTestPaths())))
			->addRow(array('Code coverage', $runner->codeCoverageIsEnabled() === true ? 'Lines coverage' : 'Disabled'))
		;

		if ($runner->branchesAndPathsCoverageIsEnabled() === true) {
			$this->table->addRow(array('', 'Branches and paths coverage'));
		}

		if ($runner->codeCoverageIsEnabled())
		{
			$coverage = $runner->getCoverage();
			$excludedNamespaces = $coverage->getExcludedNamespaces();
			$excludedClasses = $coverage->getExcludedClasses();
			$excludedMethods = $coverage->getExcludedMethods();

			$this->table
				->addRow(array('CC excluded namespaces', sizeof($excludedNamespaces) === 0 ? 'N/A' : $excludedNamespaces))
				->addRow(array('CC excluded classes', sizeof($excludedClasses) === 0 ? 'N/A' : $excludedClasses))
				->addRow(array('CC excluded methods', sizeof($excludedMethods) === 0 ? 'N/A' : $excludedMethods))
			;
		}

		return $this;
	}
}