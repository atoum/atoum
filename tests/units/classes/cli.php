<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class cli extends atoum\test
{
	public function testIsTerminalWhenOsIsNotWindowsAndStdoutIsUndefined()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = function($constant) {
					switch ($constant)
					{
						case 'PHP_WINDOWS_VERSION_BUILD':
							return false;

						case 'STDOUT':
							return false;
					}
				}
			)
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isFalse()
		;
	}


	public function testIsTerminalWhenOsIsNotWindowsStdoutIsTtyIsUndefined()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = function($constant) {
					switch ($constant)
					{
						case 'PHP_WINDOWS_VERSION_BUILD':
							return false;

						case 'STDOUT':
							return true;
					}
				}
			)
			->and($adapter->function_exists = function($function) { return ($function != 'posix_isatty'); })
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isFalse()
		;
	}

	public function testIsTerminalWhenOsIsNotWindowsStdoutIsTtyIsReturnFalse()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = function($constant) {
					switch ($constant)
					{
						case 'PHP_WINDOWS_VERSION_BUILD':
							return false;

						case 'STDOUT':
							return true;
					}
				}
			)
			->and($adapter->function_exists = function($function) { return ($function == 'posix_isatty'); })
			->and($adapter->constant = uniqid())
			->and($adapter->posix_isatty = false)
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isFalse()
		;
	}

	public function testIsTerminalWhenOsIsNotWindowsStdoutIsTtyIsReturnTrue()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = function($constant) {
					switch ($constant)
					{
						case 'PHP_WINDOWS_VERSION_BUILD':
							return false;

						case 'STDOUT':
							return true;
					}
				}
			)
			->and($adapter->function_exists = function($function) { return ($function == 'posix_isatty'); })
			->and($adapter->constant = uniqid())
			->and($adapter->posix_isatty = true)
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isTrue()
		;
	}

	public function testIsTerminalWhenOsIsWindowsAndAnsiconIsUndefined()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = function($constant) {
					switch ($constant)
					{
						case 'PHP_WINDOWS_VERSION_BUILD':
							return true;

						default:
							return false;
					}
				}
			)
			->and($adapter->getenv = function($variable) { return ($variable != 'ANSICON'); })
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isFalse()
		;
	}

	public function testIsTerminalWhenOsIsWindowsAndAnsiconIsDefined()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = function($constant) {
					switch ($constant)
					{
						case 'PHP_WINDOWS_VERSION_BUILD':
							return true;

						default:
							return false;
					}
				}
			)
			->and($adapter->getenv = function($variable) { return ($variable == 'ANSICON'); })
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isTrue()
		;
	}

	public function testIsTerminalWhenForceTerminalWasUsedAfterFirstCallToConstructor()
	{
		$this
			->if($adapter = new atoum\test\adapter())
			->and($adapter->defined = function($constant) {
					switch ($constant)
					{
						case 'PHP_WINDOWS_VERSION_BUILD':
							return false;

						case 'STDOUT':
							return false;
					}
				}
			)
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isFalse()
			->if($otherCli = new atoum\cli($adapter))
			->then
				->boolean($otherCli->isTerminal())->isFalse()
			->if(\mageekguy\atoum\cli::forceTerminal())
			->then
				->boolean($cli->isTerminal())->isTrue()
				->boolean($otherCli->isTerminal())->isTrue()
		;
	}

	public function testIsTerminalWhenForceTerminalWasUsedBeforeFirstCallToConstructor()
	{
		$this
			->if(\mageekguy\atoum\cli::forceTerminal())
			->and($adapter = new atoum\test\adapter())
			->and($adapter->defined = function($constant) {
					switch ($constant)
					{
						case 'PHP_WINDOWS_VERSION_BUILD':
							return false;

						case 'STDOUT':
							return false;
					}
				}
			)
			->and($cli = new atoum\cli($adapter))
			->then
				->boolean($cli->isTerminal())->isTrue()
			->if($otherCli = new atoum\cli())
			->then
				->boolean($otherCli->isTerminal())->isTrue()
		;
	}
}
