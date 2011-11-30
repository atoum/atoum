<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

require __DIR__ . '/../runner.php';

class includer extends atoum\test
{
	public function test__construct()
	{
		$this->assert
			->if($includer = new atoum\includer())
			->then
				->variable($includer->getErrorHandler())->isNull()
			->if($includer = new atoum\includer($errorHandler = function() {}))
			->then
				->object($includer->getErrorHandler())->isIdenticalTo($errorHandler)
		;
	}

	public function testSetErrorHandler()
	{
		$this->assert
			->if($includer = new atoum\includer())
			->then
				->object($includer->setErrorHandler($errorHandler = function() {}))->isIdenticalTo($includer)
				->object($includer->getErrorHandler())->isIdenticalTo($errorHandler)
				->object($includer->setErrorHandler($otherErrorHandler = function() {}))->isIdenticalTo($includer)
				->object($includer->getErrorHandler())->isIdenticalTo($otherErrorHandler)
			->if($includer = new atoum\includer(function() {}))
			->then
				->object($includer->setErrorHandler($errorHandler = function() {}))->isIdenticalTo($includer)
				->object($includer->getErrorHandler())->isIdenticalTo($errorHandler)
		;
	}

	public function testIncludeOnce()
	{
		$this->assert
			->if($includer = new atoum\includer())
			->and($fileToIncludeController = mock\stream::get('file/to/include'))
			->and($fileToIncludeController->file_get_contents = $fileContents = uniqid())
			->then
				->object($includer->includeOnce('atoum://file/to/include'))->isIdenticalTo($includer)
				->output->isEqualTo($fileContents)
			->if($fileToIncludeController->file_get_contents = false)
				->object($includer->includeOnce('atoum://file/to/include'))->isIdenticalTo($includer)
				->error
					->withType(E_WARNING)
					->withPattern('%include_once\(atoum://file/to/include\): failed to open stream%')
						->exists()
					->withPattern('%include_once\(\): Failed opening \'atoum://file/to/include\' for inclusion%')
						->exists()
			->if($includer->setErrorHandler(function() {}))
			->and($fileToIncludeController = mock\stream::get('other/file/to/include'))
			->and($fileToIncludeController->file_get_contents = $fileContents = uniqid())
			->then
				->object($includer->includeOnce('atoum://other/file/to/include'))->isIdenticalTo($includer)
				->output->isEqualTo($fileContents)
			->if($fileToIncludeController->file_get_contents = false)
				->object($includer->includeOnce('atoum://other/file/to/include'))->isIdenticalTo($includer)
				->error->notExists()
		;
	}
}

?>
