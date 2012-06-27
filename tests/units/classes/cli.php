<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class cli extends atoum\test
{
	public function testIsTerminalWhenStdoutIsUndefined()
	{
		$adapter = new atoum\test\adapter();
		$adapter->defined = false;

		$cli = new atoum\cli($adapter);

		$this->assert
			->boolean($cli->isTerminal())->isFalse()
		;
	}

	public function testIsTerminalWhenPosixIsTtyIsUndefined()
	{
		$adapter = new atoum\test\adapter();
		$adapter->defined = true;
		$adapter->function_exists = false;

		$cli = new atoum\cli($adapter);

		$this->assert
			->boolean($cli->isTerminal())->isFalse()
		;
	}

	public function testIsTerminalWhenPosixIsTtyReturnFalse()
	{
		$adapter = new atoum\test\adapter();
		$adapter->defined = true;
		$adapter->function_exists = true;
		$adapter->constant = null;
		$adapter->posix_isatty = false;

		$cli = new atoum\cli($adapter);

		$this->assert
			->boolean($cli->isTerminal())->isFalse()
		;
	}

	public function testIsTerminalWhenPosixIsTtyReturnTrue()
	{
		$adapter = new atoum\test\adapter();
		$adapter->defined = true;
		$adapter->function_exists = true;
		$adapter->constant = null;
		$adapter->posix_isatty = true;

		$cli = new atoum\cli($adapter);

		$this->assert
			->boolean($cli->isTerminal())->isTrue()
		;
	}

	public function testIsTerminalWhenForceTerminalWasUsedAfterFirstCallToConstructor()
	{
		$adapter = new atoum\test\adapter();
		$adapter->defined = false;

		$cli = new atoum\cli($adapter);

		$this->assert
			->boolean($cli->isTerminal())->isFalse()
		;

		$otherCli = new atoum\cli($adapter);

		$this->assert
			->boolean($otherCli->isTerminal())->isFalse()
		;

		\mageekguy\atoum\cli::forceTerminal();

		$this->assert
			->boolean($cli->isTerminal())->isTrue()
			->boolean($otherCli->isTerminal())->isTrue()
		;
	}

	public function testIsTerminalWhenForceTerminalWasUsedBeforeFirstCallToConstructor()
	{
		\mageekguy\atoum\cli::forceTerminal();

		$adapter = new atoum\test\adapter();
		$adapter->defined = false;

		$cli = new atoum\cli($adapter);

		$this->assert
			->boolean($cli->isTerminal())->isTrue()
		;

		$otherCli = new atoum\cli();

		$this->assert
			->boolean($otherCli->isTerminal())->isTrue()
		;
	}
}
