<?php

namespace mageekguy\atoum\tests\units\fcgi;

use
	mageekguy\atoum,
	mageekguy\atoum\fcgi,
	mageekguy\atoum\fcgi\request as testedClass
;

require_once __DIR__ . '/../../runner.php';

class request extends atoum\test
{
	public function test__set()
	{
		$this
			->if($request = new testedClass())
			->and($request->stdin = $stdin = uniqid())
			->then
				->string($request->getStdin())->isIdenticalTo($stdin)
			->if($request->content_length = $contentLength = uniqid())
			->then
				->array($request->getParams())->isEqualTo(array(
						'content_length' => $contentLength
					)
				)
		;
	}

	public function test__get()
	{
		$this
			->if($request = new testedClass())
			->then
				->string($request->stdin)->isEmpty()
			->if($request->stdin = $stdin = uniqid())
			->then
				->string($request->stdin)->isEqualTo($stdin)
			->if($request->content_length = $contentLength = uniqid())
			->then
				->string($request->content_length)->isEqualTo($contentLength)
		;
	}

	public function test__isset()
	{
		$this
			->if($request = new testedClass())
			->then
				->boolean(isset($request->stdin))->isFalse()
				->boolean(isset($request->content_length))->isFalse()
				->boolean(isset($request->CONTENT_LENGTH))->isFalse()
			->if($request->stdin = uniqid())
			->then
				->boolean(isset($request->stdin))->isTrue()
				->boolean(isset($request->content_length))->isFalse()
				->boolean(isset($request->CONTENT_LENGTH))->isFalse()
			->if($request->content_length = uniqid())
			->then
				->boolean(isset($request->stdin))->isTrue()
				->boolean(isset($request->content_length))->isTrue()
		;
	}

	public function test__unset()
	{
		$this
			->if($request = new testedClass())
			->when(function() use ($request) { unset($request->stdin); })
			->then
				->boolean(isset($request->stdin))->isFalse()
			->when(function() use ($request) { unset($request->content_length); })
			->then
				->boolean(isset($request->content_length))->isFalse()
			->when(function() use ($request) { unset($request->CONTENT_LENGTH); })
			->then
				->boolean(isset($request->CONTENT_LENGTH))->isFalse()
			->if($request->stdin = uniqid())
			->when(function() use ($request) { unset($request->stdin); })
			->then
				->boolean(isset($request->stdin))->isFalse()
			->when(function() use ($request) { unset($request->content_length); })
			->then
				->boolean(isset($request->content_length))->isFalse()
			->when(function() use ($request) { unset($request->CONTENT_LENGTH); })
			->then
				->boolean(isset($request->CONTENT_LENGTH))->isFalse()
			->if($request->content_length = uniqid())
			->when(function() use ($request) { unset($request->stdin); })
			->then
				->boolean(isset($request->stdin))->isFalse()
			->when(function() use ($request) { unset($request->content_length); })
			->then
				->boolean(isset($request->content_length))->isFalse()
			->when(function() use ($request) { unset($request->CONTENT_LENGTH); })
			->then
				->boolean(isset($request->CONTENT_LENGTH))->isFalse()
		;
	}
}
