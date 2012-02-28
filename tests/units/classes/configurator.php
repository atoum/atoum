<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class configurator extends atoum\test
{
	public function test__construct()
	{
		$this
			->mock('mageekguy\atoum\runner')
			->mock('mageekguy\atoum\scripts\runner')
			->assert
				->if($script = new atoum\scripts\runner(uniqid()))
				->and($configurator = new atoum\configurator($script))
				->then
					->object($configurator->getScript())->isIdenticalTo($script)
		;
	}

	public function test__call()
	{
		$this
			->mock('mageekguy\atoum\runner')
			->mock('mageekguy\atoum\scripts\runner')
			->assert
				->if($runner = new \mock\mageekguy\atoum\runner())
				->and($runner->getMockController()->setBootstrapFile = function() {})
				->and($script = new \mock\mageekguy\atoum\scripts\runner(uniqid()))
				->and($script->setRunner($runner))
				->and($configurator = new atoum\configurator($script))
				->then
					->object($configurator->setScoreFile($scoreFile = uniqid()))->isIdenticalTo($configurator)
					->mock($script)->call('setScoreFile')->withArguments($scoreFile)->once()
					->object($configurator->setPhpPath($phpPath = uniqid()))->isIdenticalTo($configurator)
					->mock($runner)->call('setPhpPath')->withArguments($phpPath)->once()
					->object($configurator->setBf($bootstrapFile = uniqid()))->isIdenticalTo($configurator)
					->mock($runner)->call('setBootstrapFile')->withArguments($bootstrapFile)->once()
				->exception(function() use ($configurator, & $method) { $configurator->{$method = uniqid()}(); })
					->isInstanceOf('mageekguy\atoum\exceptions\runtime\unexpectedValue')
					->hasMessage('Method \'' . $method . '\' is unavailable')
		;
	}
}

?>
