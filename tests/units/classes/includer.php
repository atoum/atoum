<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\stream
;

require __DIR__ . '/../runner.php';

class includer extends atoum\test
{
	public function testIncludePath()
	{
		$this->assert
			->if($includer = new atoum\includer())
			->and($unknownFile = stream::get())
			->then
				->exception(function() use ($includer, $unknownFile) { $includer->includePath($unknownFile); })
					->isInstanceOf('mageekguy\atoum\includer\exception')
					->hasMessage('Unable to include \'' . $unknownFile . '\'')
			->if($file = stream::get())
			->and($file->file_get_contents = $fileContents = uniqid())
			->then
				->object($includer->includePath($file))->isIdenticalTo($includer)
				->output->isEqualTo($fileContents)
			->if($fileWithError = stream::get())
			->and($fileWithError->file_get_contents = '<?php trigger_error(\'' . ($message = uniqid()) . '\', E_USER_WARNING); ?>')
			->then
				->object($includer->includePath($fileWithError))->isIdenticalTo($includer)
				->error
					->withType(E_USER_WARNING)
					->withMessage($message)
						->exists()
		;
	}
}
