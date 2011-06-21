<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium;

use
	\mageekguy\atoum,
	\mageekguy\atoum\tests\functional\selenium  as s
;

require_once(__DIR__ . '/../../../runner.php');

class html extends atoum\test
{
	public function testUnableToGetTitleIfWebDriverIsNotSet()
	{
		$html = new s\html('http://www.atoum.org');
		
		$this->assert
			->exception(function() use($html) {
						$html->getTitle();
					}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('webDriver must be set');
		;
	}
}

?>
