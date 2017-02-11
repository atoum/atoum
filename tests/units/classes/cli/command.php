<?php

namespace mageekguy\atoum\tests\units\cli;

require_once __DIR__ . '/../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\cli\command as testedClass
;

class command extends atoum
{

	public function test__construct()
	{
		$this
			->if($command = new testedClass())
			->then
				->string($command->getBinaryPath())->isEmpty()
				->object($command->getAdapter())->isEqualTo(new atoum\adapter())
		;
	}

	public function test__toString()
	{
		$this
			->if($command = new testedClass('/usr/bin/php5'))
			->and($command->addOption('-f', './vendor/bin/atoum'))
			->and($command->addArgument('--disable-loop-mode'))
			->and($command->addArgument('--force-terminal'))
			->and($command->addArgument('-f', 'toto_test.php'))
			->and($command->addArgument('--tags', 'test'))
			->and($command->addArgument('--score-file', '/tmp/atoum.score'))
			->then
				->string((string)$command)
					->isEqualTo("/usr/bin/php5 -f ./vendor/bin/atoum -- --disable-loop-mode --force-terminal -f 'toto_test.php' --tags 'test' --score-file '/tmp/atoum.score'")
		;

		$this
			->if($command = new testedClass('/usr/bin/php5'))
			->and($command->addOption('-f', './vendor/bin/atoum'))
			->and($command->addArgument('--disable-loop-mode'))
			->and($command->addArgument('--force-terminal'))
			->and($command->addArgument('-f', 'toto_test.php'))
			->and($command->addArgument('--filter', "'test1' in tags"))
			->and($command->addArgument('--score-file', '/tmp/atoum.score'))
			->then
				->string((string)$command)
					->isEqualTo("/usr/bin/php5 -f ./vendor/bin/atoum -- --disable-loop-mode --force-terminal -f 'toto_test.php' --filter ''\''test1'\'' in tags' --score-file '/tmp/atoum.score'")
		;

	}
}
