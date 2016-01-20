<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\php as testedClass
;

require_once __DIR__ . '/../runner.php';

class php extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($php = new testedClass())
			->then
				->string($php->getBinaryPath())->isNotEmpty()
				->object($php->getAdapter())->isEqualTo(new atoum\adapter())
				->string($php->getStdout())->isEmpty()
				->string($php->getStderr())->isEmpty()
				->array($php->getOptions())->isEmpty()
				->array($php->getArguments())->isEmpty()
			->if($php = new testedClass($phpPath = uniqid(), $adapter = new atoum\adapter()))
			->then
				->string($php->getBinaryPath())->isEqualTo($phpPath)
				->object($php->getAdapter())->isIdenticalTo($adapter)
				->string($php->getStdout())->isEmpty()
				->string($php->getStderr())->isEmpty()
				->array($php->getOptions())->isEmpty()
				->array($php->getArguments())->isEmpty()
		;
	}

	public function test__toString()
	{
		$this
			->if($php = new testedClass())
			->then
				->castToString($php)->isEqualTo(defined('PHP_WINDOWS_VERSION_MAJOR') === true ? '"' . $php->getBinaryPath() . '"' : escapeshellcmd($php->getBinaryPath()))
			->if($php->addOption($option1 = uniqid()))
			->then
				->castToString($php)->isEqualTo((defined('PHP_WINDOWS_VERSION_MAJOR') === true ? '"' . $php->getBinaryPath() . '"' : escapeshellcmd($php->getBinaryPath())) . ' ' . escapeshellcmd($option1))
			->if($php->addOption($option2 = uniqid(), $option2Value = uniqid() . ' ' . uniqid()))
			->then
				->castToString($php)->isEqualTo((defined('PHP_WINDOWS_VERSION_MAJOR') === true ? '"' . $php->getBinaryPath() . '"' : escapeshellcmd($php->getBinaryPath())) . ' ' . escapeshellcmd($option1 . ' ' . $option2 . ' ' . $option2Value))
			->if($php->addArgument($argument1 = uniqid()))
			->then
				->castToString($php)->isEqualTo((defined('PHP_WINDOWS_VERSION_MAJOR') === true ? '"' . $php->getBinaryPath() . '"' : escapeshellcmd($php->getBinaryPath())) . ' ' . escapeshellcmd($option1 . ' ' . $option2 . ' ' . $option2Value . ' -- ' . $argument1))
			->if($php->addArgument($argument2 = uniqid(), $argument2Value = uniqid()))
			->then
				->castToString($php)->isEqualTo((defined('PHP_WINDOWS_VERSION_MAJOR') === true ? '"' . $php->getBinaryPath() . '"' : escapeshellcmd($php->getBinaryPath())) . ' ' . escapeshellcmd($option1 . ' ' . $option2 . ' ' . $option2Value . ' -- ' . $argument1 . ' ' . $argument2) . ' ' . (defined('PHP_WINDOWS_VERSION_MAJOR') === true ? '"' . $argument2Value . '"': escapeshellarg($argument2Value)))
		;
	}

	public function testSetAdapter()
	{
		$this
			->given($php = new testedClass())
			->then
				->object($php->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($php)
				->object($php->getAdapter())->isIdenticalTo($adapter)
				->object($php->setAdapter())->isIdenticalTo($php)
				->object($php->getAdapter())
					->isNotIdenticalTo($adapter)
					->isEqualTo(new atoum\adapter())
		;
	}

	public function testReset()
	{
		$this
			->if($php = new testedClass())
			->then
				->object($php->reset())->isIdenticalTo($php)
				->string($php->getBinaryPath())->isNotEmpty()
				->array($php->getOptions())->isEmpty()
				->array($php->getArguments())->isEmpty()
			->if($php->setBinaryPath($binaryPath = uniqid()))
			->and($php->addOption(uniqid()))
			->and($php->addArgument(uniqid()))
			->then
				->object($php->reset())->isIdenticalTo($php)
				->string($php->getBinaryPath())->isEqualTo($binaryPath)
				->array($php->getOptions())->isEmpty()
				->array($php->getArguments())->isEmpty()
		;
	}

	public function testSetBinaryPath()
	{
		$this
			->given($php = new atoum\php(null, $adapter = new atoum\test\adapter()))
			->if($adapter->defined = function($constant) { return ($constant == 'PHP_BINARY'); })
			->and($adapter->constant = function($constant) use (& $phpBinary) { return ($constant != 'PHP_BINARY' ? null : $phpBinary = uniqid()); })
			->then
				->object($php->setBinaryPath())->isIdenticalTo($php)
				->string($php->getBinaryPath())->isEqualTo($phpBinary)
			->if($adapter->defined = false)
			->and($adapter->constant = null)
			->and($adapter->getenv = function($variable) use (& $pearBinaryPath) { return ($variable != 'PHP_PEAR_PHP_BIN' ? false : $pearBinaryPath = uniqid()); })
			->then
				->object($php->setBinaryPath())->isIdenticalTo($php)
				->string($php->getBinaryPath())->isEqualTo($pearBinaryPath)
			->if($adapter->getenv = function($variable) use (& $phpBinPath) {
					switch ($variable)
					{
						case 'PHPBIN':
							return ($phpBinPath = uniqid());

						default:
							return false;
					}
				}
			)
			->then
				->object($php->setBinaryPath())->isIdenticalTo($php)
				->string($php->getBinaryPath())->isEqualTo($phpBinPath)
			->if($adapter->constant = function($constant) use (& $phpBinDir) { return ($constant != 'PHP_BINDIR' ? null : $phpBinDir = uniqid()); })
			->and($adapter->getenv = false)
			->then
				->object($php->setBinaryPath())->isIdenticalTo($php)
				->string($php->getBinaryPath())->isEqualTo($phpBinDir . '/php')
				->object($php->setBinaryPath($phpPath = uniqid()))->isIdenticalTo($php)
				->string($php->getBinaryPath())->isEqualTo($phpPath)
		;
	}

	public function testAddOption()
	{
		$this
			->if($php = new testedClass())
			->then
				->object($php->addOption($optionName = uniqid()))->isIdenticalTo($php)
				->array($php->getOptions())->isEqualTo(array($optionName => null))
				->object($php->addOption($optionName))->isIdenticalTo($php)
				->array($php->getOptions())->isEqualTo(array($optionName => null))
				->object($php->addOption($otherOptionName = uniqid()))->isIdenticalTo($php)
				->array($php->getOptions())->isEqualTo(array($optionName => null, $otherOptionName => null))
				->object($php->addOption($anotherOptionName = uniqid(), $optionValue = uniqid()))->isIdenticalTo($php)
				->array($php->getOptions())->isEqualTo(array($optionName => null, $otherOptionName => null, $anotherOptionName => $optionValue))
				->object($php->addOption($anotherOptionName, $anotherOptionValue = uniqid()))->isIdenticalTo($php)
				->array($php->getOptions())->isEqualTo(array($optionName => null, $otherOptionName => null, $anotherOptionName => $anotherOptionValue))
				->object($php->addOption($emptyOption = uniqid(), ''))->isIdenticalTo($php)
				->array($php->getOptions())->isEqualTo(array($optionName => null, $otherOptionName => null, $anotherOptionName => $anotherOptionValue, $emptyOption => null))
		;
	}

	public function testAddArgument()
	{
		$this
			->if($php = new testedClass())
			->then
				->object($php->addArgument($argument1 = uniqid()))->isIdenticalTo($php)
				->array($php->getArguments())->isEqualTo(array(array($argument1 => null)))
				->object($php->addArgument($argument1))->isIdenticalTo($php)
				->array($php->getArguments())->isEqualTo(array(array($argument1 => null), array($argument1 => null)))
				->object($php->addArgument($argument2 = uniqid()))->isIdenticalTo($php)
				->array($php->getArguments())->isEqualTo(array(array($argument1 => null), array($argument1 => null), array($argument2 => null)))
				->object($php->addArgument($emptyArgument = uniqid(), ''))->isIdenticalTo($php)
				->array($php->getArguments())->isEqualTo(array(array($argument1 => null), array($argument1 => null), array($argument2 => null), array($emptyArgument => null)))
		;
	}

	public function testRun()
	{
		$this
			->if($php = new testedClass($phpPath = uniqid(), $adapter = new atoum\test\adapter()))
			->and($adapter->proc_open = false)
			->then
				->exception(function() use ($php, & $code) { $php->run($code = uniqid()); })
					->isInstanceOf('mageekguy\atoum\cli\command\exception')
					->hasMessage('Unable to run \'' . $php . '\'')
				->adapter($adapter)
					->call('proc_open')->withArguments((string) $php, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), array())->once()
			->if($php = new testedClass($phpPath = uniqid(), $adapter))
			->and($code = uniqid())
			->and($adapter->proc_open = function($command, $descriptors, & $streams) use (& $phpResource, & $stdin, & $stdout, & $stderr) { $streams = array($stdin = uniqid(), $stdout = uniqid(), $stderr = uniqid); return ($phpResource = uniqid()); })
			->and($adapter->fwrite = strlen($code))
			->and($adapter->fclose = null)
			->and($adapter->stream_set_blocking = null)
			->then
				->object($php->run($code))->isIdenticalTo($php)
				->adapter($adapter)
					->call('proc_open')->withArguments((string) $php, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), array())->once()
					->call('fwrite')->withArguments($stdin, $code, strlen($code))->once()
					->call('fclose')->withArguments($stdin)->once()
					->call('stream_set_blocking')->withArguments($stdout)->once()
					->call('stream_set_blocking')->withArguments($stderr)->once()
			->if($php = new testedClass($phpPath = uniqid(), $adapter))
			->and($adapter->resetCalls())
			->and($adapter->fwrite[1] = 4)
			->and($adapter->fwrite[2] = strlen($code) - 4)
			->then
				->object($php->run($code))->isIdenticalTo($php)
				->adapter($adapter)
					->call('proc_open')->withArguments((string) $php, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), array())->once()
					->call('fwrite')->withArguments($stdin, $code, strlen($code))->once()
					->call('fwrite')->withArguments($stdin, substr($code, 4), strlen($code) - 4)->once()
					->call('fclose')->withArguments($stdin)->once()
					->call('stream_set_blocking')->withArguments($stdout)->once()
					->call('stream_set_blocking')->withArguments($stderr)->once()
			->if($php = new testedClass($phpPath = uniqid(), $adapter))
			->and($php->addOption('firstOption'))
			->then
				->object($php->run($code))->isIdenticalTo($php)
				->adapter($adapter)
					->call('proc_open')->withArguments((string) $php, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), array())->once()
			->if($php = new testedClass($phpPath = uniqid(), $adapter))
			->and($php->addOption('firstOption'))
			->and($php->addOption('secondOption', 'secondOptionValue'))
			->then
				->object($php->run($code))->isIdenticalTo($php)
				->adapter($adapter)
					->call('proc_open')->withArguments((string) $php, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), array())->once()
			->if($php = new testedClass($phpPath = uniqid(), $adapter))
			->and($php->addArgument($argument1 = uniqid()))
			->and($php->addArgument($argument2 = uniqid()))
			->then
				->object($php->run($code))->isIdenticalTo($php)
				->adapter($adapter)
					->call('proc_open')->withArguments((string) $php, array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')), array())->once()
		;
	}

	public function testGetExitCode()
	{
		$this
			->if($php = new testedClass(null, $adapter = new atoum\test\adapter()))
			->then
				->variable($php->getExitCode())->isNull()
			->if($adapter->proc_open = function($command, $descriptors, & $streams) use (& $phpResource, & $stdin, & $stdout, & $stderr) { $streams = array($stdin = uniqid(), $stdout = uniqid(), $stderr = uniqid); return ($phpResource = uniqid()); })
			->and($adapter->fclose = null)
			->and($adapter->stream_set_blocking = null)
			->and($adapter->stream_get_contents = null)
			->and($adapter->proc_close = null)
			->and($adapter->proc_get_status[1] = array('running' => true))
			->and($adapter->proc_get_status[2] = array('running' => false, 'exitcode' => $exitCode = rand(0, PHP_INT_MAX)))
			->and($php->run())
			->then
				->variable($php->getExitCode())->isEqualTo($exitCode)
		;
	}
}
