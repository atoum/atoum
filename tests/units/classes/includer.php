<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum,
	mageekguy\atoum\mock
;

require __DIR__ . '/../runner.php';

class includer extends atoum\test
{
	public function testIncludeOnce()
	{
		$this->assert
			->if($includer = new atoum\includer())
			->and($fileNotExistsController = mock\stream::get('file/not/exists'))
			->then
				->exception(function() use ($includer) { $includer->includePath('atoum://file/not/exists'); })
					->isInstanceOf('mageekguy\atoum\includer\exception')
					->hasMessage('Unable to include \'atoum://file/not/exists\'')
			->if($fileExistsController = mock\stream::get('file/exists'))
			->and($fileExistsController->file_get_contents = $fileContents = uniqid())
				->object($includer->includePath('atoum://file/exists'))->isIdenticalTo($includer)
				->output->isEqualTo($fileContents)
			->if($fileWithAnErrorController = mock\stream::get('file/with/an/error'))
			->and($fileWithAnErrorController->file_get_contents = '<?php trigger_error(\'' . ($message = uniqid()) . '\', E_USER_WARNING); ?>')
				->object($includer->includePath('atoum://file/with/an/error'))->isIdenticalTo($includer)
				->error
					->withType(E_USER_WARNING)
					->withMessage($message)
						->exists()
		;
	}
}

?>
