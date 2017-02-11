<?php

namespace mageekguy\atoum\script\visitors;

use 
	mageekguy\atoum,
	mageekguy\atoum\cli\table,
	mageekguy\atoum\script,
	mageekguy\atoum\scripts,
	mageekguy\atoum\script\visitor
;

class verbose implements visitor
{
	private $table;

	public function __construct(table $table = null)
	{
		$this->table = $table ?: new table(array('Script property', 'value'));
	}

	public function __toString()
	{
		return (string) $this->table;
	}

	public function visitScript(script $script)
	{
		$autoloaders = atoum\autoloader::getRegisteredAutoloaders();
		$this->table
			->addRow(array('Autoloader cache file', sizeof($autoloaders) === 0 ? 'N/A' : array_map(function(atoum\autoloader $autoloader) { return $autoloader->getCacheFileForInstance(); }, $autoloaders)))
			->addRow(array('Script class', get_class($script)))
			->addRow(array('CLI arguments', sizeof($script->getArgumentsParser()) === 0 ? 'N/A' : $script->getArgumentsParser()))
		;

		return $this;
	}

	public function visitConfigurable(script\configurable $configurable)
	{
		$this
			->visitScript($configurable)
			->table
				->addRow(array('Configuration file', sizeof($configurable->getConfigFiles()) === 0 ? 'N/A' : $configurable->getConfigFiles()))
		;

		return $this;
	}

	public function visitRunner(scripts\runner $runner)
	{
		$this
			->visitConfigurable($runner)
			->table
				->addRow(array('Accepted tags', sizeof($runner->getTestedTags()) === 0 ? 'N/A' : $runner->getTestedTags()))
				->addRow(array('Accepted namespaces', sizeof($runner->getTestedNamespaces()) === 0 ? 'N/A' : $runner->getTestedNamespaces()))
		;

		$testedMethods = array_map(
			function($testedClass, $testedMethods) {
				return array_map(
					function($testedMethod) use ($testedClass) {
						return $testedClass . '::' . $testedMethod;
					} ,
					$testedMethods
				);
			},
			array_keys($runner->getTestedMethods()),
			array_values($runner->getTestedMethods())
		);

		$this->table->addRow(array('Accepted methods', sizeof($testedMethods) === 0 ? 'N/A' : $testedMethods));

		return $this;
	}
}