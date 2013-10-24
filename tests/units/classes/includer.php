<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\stream,
	mageekguy\atoum\includer as testedClass
;

require __DIR__ . '/../runner.php';

class includer extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($includer = new testedClass())
			->then
				->object($includer->getAdapter())->isEqualTo(new atoum\adapter())
				->array($includer->getErrors())->isEmpty()
			->if($includer = new testedClass($adapter = new atoum\adapter()))
			->then
				->object($includer->getAdapter())->isIdenticalTo($adapter)
				->array($includer->getErrors())->isEmpty()
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($includer = new testedClass())
			->then
				->object($includer->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($includer)
				->object($includer->getAdapter())->isIdenticalTo($adapter)
				->object($includer->setAdapter())->isIdenticalTo($includer)
				->object($includer->getAdapter())
					->isEqualTo(new atoum\adapter())
					->isNotIdenticalTo($adapter)
		;
	}

	public function testResetErrors()
	{
		$this
			->if($includer = new testedClass())
			->then
				->object($includer->resetErrors())->isIdenticalTo($includer)
				->array($includer->getErrors())->isEmpty()
			->if($includer->errorHandler(E_NOTICE, uniqid(), uniqid(), rand(1, PHP_INT_MAX), array()))
			->then
				->object($includer->resetErrors())->isIdenticalTo($includer)
				->array($includer->getErrors())->isEmpty()
		;
	}

	public function testIncludePath()
	{
		$this
			->if($includer = new testedClass($adapter = new atoum\test\adapter()))
			->and($adapter->set_error_handler = function($errorHandler) use (& $errors) { set_error_handler($errorHandler); return function($error, $message, $file, $line, $context) use (& $errors) { $errors[] = func_get_args(); };})
			->and($unknownFile = stream::get())
			->then
				->exception(function() use ($includer, $unknownFile) { $includer->includePath($unknownFile); })
					->isInstanceOf('mageekguy\atoum\includer\exception')
					->hasMessage('Unable to include \'' . $unknownFile . '\'')
				->array($includer->getErrors())->isNotEmpty()
				->adapter($adapter)
					->call('set_error_handler')->withArguments(array($includer, 'errorHandler'))->once()
					->call('restore_error_handler')->once()
			->if($file = stream::get())
			->and($file->file_get_contents = $fileContents = uniqid())
			->then
				->object($includer->includePath($file))->isIdenticalTo($includer)
				->output->isEqualTo($fileContents)
				->adapter($adapter)
					->call('set_error_handler')->withArguments(array($includer, 'errorHandler'))->twice()
					->call('restore_error_handler')->twice()
				->array($includer->getErrors())->isEmpty()
			->if($fileWithError = stream::get())
			->and($fileWithError->file_get_contents = '<?php trigger_error(\'' . ($message = uniqid()) . '\', E_USER_WARNING); ?>')
			->then
				->object($includer->includePath($fileWithError))->isIdenticalTo($includer)
				->array($includer->getErrors())->isEmpty()
				->integer($errors[0][0])->isEqualTo(E_USER_WARNING)
				->string($errors[0][1])->isEqualTo($message)
				->adapter($adapter)
					->call('set_error_handler')->withArguments(array($includer, 'errorHandler'))->thrice()
					->call('restore_error_handler')->thrice()
			->if($fileWithError = stream::get())
			->and($fileWithError->file_get_contents = '<?php @trigger_error(\'' . ($message = uniqid()) . '\', E_USER_WARNING); ?>')
			->and($errors = array())
			->then
				->object($includer->includePath($fileWithError))->isIdenticalTo($includer)
				->array($includer->getErrors())->isEmpty()
				->array($errors)->isEmpty()
				->adapter($adapter)
					->call('set_error_handler')->withArguments(array($includer, 'errorHandler'))->exactly(4)
					->call('restore_error_handler')->exactly(4)
			->if($adapter->set_error_handler = function($errorHandler) { set_error_handler($errorHandler); return null; })
			->and($fileWithError = stream::get())
			->and($fileWithError->file_get_contents = '<?php trigger_error(\'' . ($message = uniqid()) . '\', E_USER_WARNING); ?>')
			->then
				->object($includer->includePath($fileWithError))->isIdenticalTo($includer)
				->array($errors = $includer->getErrors())->isNotEmpty()
				->integer($errors[0][0])->isEqualTo(E_USER_WARNING)
				->string($errors[0][1])->isEqualTo($message)
				->adapter($adapter)
					->call('set_error_handler')->withArguments(array($includer, 'errorHandler'))->exactly(5)
					->call('restore_error_handler')->exactly(5)
		;
	}

	public function testErrorHandler()
	{
		$this
			->if($includer = new testedClass($adapter = new atoum\test\adapter()))
			->and($adapter->error_reporting = E_ALL)
			->then
				->boolean($includer->errorHandler($errno = E_NOTICE, $message = uniqid(), $file = uniqid(), $line = rand(1, PHP_INT_MAX), $context = array()))->isTrue()
				->array($includer->getErrors())->isEqualTo(array(
						array($errno, $message, $file, $line, $context)
					)
				)
				->boolean($includer->errorHandler($otherErrno = E_WARNING, $otherMessage = uniqid(), $otherFile = uniqid(), $otherLine = rand(1, PHP_INT_MAX), $otherContext = array()))->isTrue()
				->array($includer->getErrors())->isEqualTo(array(
						array($errno, $message, $file, $line, $context),
						array($otherErrno, $otherMessage, $otherFile, $otherLine, $otherContext)
					)
				)
				->if($includer->resetErrors())
				->then
					->boolean($includer->errorHandler(E_USER_NOTICE, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->array($includer->getErrors())->isNotEmpty()
				->if($includer->resetErrors())
				->then
					->boolean($includer->errorHandler(E_USER_WARNING, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->array($includer->getErrors())->isNotEmpty()
				->if($includer->resetErrors())
				->then
					->boolean($includer->errorHandler(E_DEPRECATED, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->array($includer->getErrors())->isNotEmpty()
				->if($adapter->error_reporting = E_ALL & ~E_DEPRECATED)
				->and($includer->resetErrors())
				->then
					->boolean($includer->errorHandler(E_NOTICE, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->array($includer->getErrors())->isNotEmpty()
				->if($includer->resetErrors())
				->then
					->boolean($includer->errorHandler(E_WARNING, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->array($includer->getErrors())->isNotEmpty()
				->if($includer->resetErrors())
				->then
					->boolean($includer->errorHandler(E_USER_NOTICE, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->array($includer->getErrors())->isNotEmpty()
				->if($includer->resetErrors())
				->then
					->boolean($includer->errorHandler(E_USER_WARNING, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->array($includer->getErrors())->isNotEmpty()
				->if($includer->resetErrors())
				->then
					->boolean($includer->errorHandler(E_DEPRECATED, $errstr = uniqid(), uniqid(), rand(1, PHP_INT_MAX), uniqid()))->isTrue()
					->array($includer->getErrors())->isEmpty()
		;
	}

	public function testGetFirstError()
	{
		$this
			->if($includer = new testedClass($adapter = new atoum\test\adapter()))
			->then
				->variable($includer->getFirstError())->isNull()
			->if($adapter->set_error_handler = function($errorHandler) { set_error_handler($errorHandler); return null; })
			->and($fileWithError = stream::get())
			->and($fileWithError->file_get_contents = '<?php trigger_error(\'' . ($message = uniqid()) . '\', E_USER_WARNING); ?>')
			->and($includer->includePath($fileWithError))
			->then
				->array($error = $includer->getFirstError())->isNotEmpty()
				->integer($error[0])->isEqualTo(E_USER_WARNING)
				->string($error[1])->isEqualTo($message)
		;
	}
}
