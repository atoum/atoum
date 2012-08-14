<?php

namespace mageekguy\atoum\tests\units\path;

use
	mageekguy\atoum,
	mageekguy\atoum\path\rewriter as testedClass
;

require_once __DIR__ . '/../../runner.php';

class rewriter extends atoum\test
{
	public function test__construct()
	{
		$this
			->if($rewriter = new testedClass())
			->then
				->array($rewriter->getMapping())->isEmpty()
		;
	}

	public function testMap()
	{
		$this
			->if($rewriter = new testedClass())
			->then
				->object($rewriter->map($from = uniqid(), $to = uniqid()))->isIdenticalTo($rewriter)
				->array($rewriter->getMapping())->isEqualTo(array($from . DIRECTORY_SEPARATOR => $to . DIRECTORY_SEPARATOR))
				->object($rewriter->map($subFrom = ($from . DIRECTORY_SEPARATOR . uniqid()), $otherTo = uniqid()))->isIdenticalTo($rewriter)
				->array($rewriter->getMapping())->isIdenticalTo(array(
						$subFrom . DIRECTORY_SEPARATOR => $otherTo . DIRECTORY_SEPARATOR,
						$from . DIRECTORY_SEPARATOR => $to . DIRECTORY_SEPARATOR
					)
				)
		;
	}

	public function testRewrite()
	{
		$this
			->if($rewriter = new testedClass())
			->then
				->string($rewriter->rewrite($file = uniqid()))->isEqualTo($file)
			->if($rewriter->map($from = uniqid(), $to = uniqid()))
			->then
				->string($rewriter->rewrite($from . DIRECTORY_SEPARATOR . ($file = uniqid())))->isEqualTo($to . DIRECTORY_SEPARATOR . $file)
			->if($rewriter->map($subFrom = ($from . DIRECTORY_SEPARATOR . uniqid()), $otherTo = uniqid()))
			->then
				->string($rewriter->rewrite($subFrom . DIRECTORY_SEPARATOR . ($otherFile = uniqid())))->isEqualTo($otherTo . DIRECTORY_SEPARATOR . $otherFile)
		;
	}
}
