<?php

namespace mageekguy\atoum\tests\units\scripts;

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\scripts,
	mageekguy\atoum\scripts\builder\vcs
;

require_once __DIR__ . '/../../runner.php';

class runner extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->isSubclassOf('mageekguy\atoum\script')
		;
	}

	public function test__construct()
	{
		$runner = new scripts\runner($name = uniqid());

		$this->assert
			->string($runner->getName())->isEqualTo($name)
			->object($runner->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->boolean(isset($runner->getAdapter()->exit))->isTrue()
			->object($runner->getLocale())->isEqualTo(new atoum\locale())
			->object($runner->getRunner())->isEqualTo(new atoum\runner())
			->variable($runner->getScoreFile())->isNull()
			->array($runner->getArguments())->isEmpty()
			->array($runner->getHelp())->isEqualTo(array(
					array(
						array('-h', '--help'),
						null,
						'Display this help'
					),
					array(
						array('-v', '--version'),
						null,
						'Display version'
					),
					array(
						array('-p', '--php'),
						'<path/to/php/binary>',
						'Path to PHP binary which must be used to run tests'
					),
					array(
						array('-drt', '--default-report-title'),
						'<string>',
						'Define default report title with <string>'
					),
					array(
						array('-c', '--configuration-files'),
						'<file>...',
						'Use all configuration files <file>'
					),
					array(
						array('-sf', '--score-file'),
						'<file>',
						'Save score in file <file>'
					),
					array(
						array('-mcn', '--max-children-number'),
						'<integer>',
						'Maximum number of sub-processus which will be run simultaneously'
					),
					array(
						array('-ncc', '--no-code-coverage'),
						null,
						'Disable code coverage'
					),
					array(
						array('-f', '--test-files'),
						'<file>...',
						'Execute all unit test files <file>'
					),
					array(
						array('-d', '--directories'),
						'<directory>...',
						'Execute unit test files in all <directory>'
					),
					array(
						array('-t', '--tags'),
						'<tag>...',
						'Execute only unit test with tags <tag>'
					),
					array(
						array('-m', '--methods'),
						'<class::method>...',
						'Execute all <class::method>, * may be used as wildcard for class name or method name'
					),
					array(
						array('-ns', '--namespaces'),
						'<namespace>...',
						'Execute all classes in all namespaces <namespace>'
					),
					array(
						array('-l', '--loop'),
						null,
						'Execute tests in an infinite loop'
					),
					array(
						array('--testIt'),
						null,
						'Execute atoum unit tests'
					)
				)
			)
		;
	}

	public function testSetArguments()
	{
		$runner = new scripts\runner($name = uniqid());

		$this->assert
			->object($runner->setArguments(array()))->isIdenticalTo($runner)
			->array($runner->getArguments())->isEmpty()
			->object($runner->setArguments($arguments = array(uniqid(), uniqid(), uniqid())))->isIdenticalTo($runner)
			->array($runner->getArguments())->isEqualTo($arguments)
		;
	}
}

?>
