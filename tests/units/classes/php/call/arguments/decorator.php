<?php

namespace mageekguy\atoum\tests\units\php\call\arguments;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\stream,
	mageekguy\atoum\php\call\arguments
;

require_once __DIR__ . '/../../../runner.php';

class decorator extends atoum\test
{
	public function testDecorate()
	{
		$this
			->if($decorator = new arguments\decorator())
			->then
				->string($decorator->decorate())->isEmpty()
				->string($decorator->decorate(null))->isEmpty()
				->string($decorator->decorate(array()))->isEmpty()
				->string($decorator->decorate(array(1)))->isEqualTo('integer(1)')
				->string($decorator->decorate(array(1, 2)))->isEqualTo('integer(1), integer(2)')
				->string($decorator->decorate(array(1.0)))->isEqualTo('float(1)')
				->string($decorator->decorate(array(1.0, 2.1)))->isEqualTo('float(1), float(2.1)')
				->string($decorator->decorate(array(true)))->isEqualTo('TRUE')
				->string($decorator->decorate(array(false)))->isEqualTo('FALSE')
				->string($decorator->decorate(array(false, true)))->isEqualTo('FALSE, TRUE')
				->string($decorator->decorate(array(null)))->isEqualTo('NULL')
				->string($decorator->decorate(array($this)))->isEqualTo('object(' . __CLASS__ . ')')
			->if($stream = stream::get())
			->and($stream->fopen = true)
			->and($resource = fopen($stream, 'r'))
			->and($dump = function() use ($resource) {
					ob_start();
					var_dump($resource);
					return ob_get_clean();
				}
			)
			->then
				->string($decorator->decorate(array($resource)))->isEqualTo($dump())
		;
	}
}
