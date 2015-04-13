<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../../runner.php';

class hash extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->extends('mageekguy\atoum\asserters\phpString');
	}

	public function testIsSha1()
	{
		$this
			->if($asserter = $this->newTestedInstance)

			->if($this->testedInstance->setWith(hash('sha1', uniqid())))
			->then
				->object($this->testedInstance->isSha1())->isTestedInstance
				->object($this->testedInstance->isSha1)->isTestedInstance

			->if($this->testedInstance->setWith(strtoupper(hash('sha1', uniqid()))))
			->then
				->object($this->testedInstance->isSha1())->isTestedInstance
				->object($this->testedInstance->isSha1)->isTestedInstance

			->if(
				$this->testedInstance
					->setWith(md5(uniqid()))
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notSha1 = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isSha1(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha1)
				->mock($locale)->call('_')->withArguments('%s should be a string of %d characters', $asserter, 40)->once

				->exception(function() use ($asserter) { $asserter->isSha1; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha1)
				->mock($locale)->call('_')->withArguments('%s should be a string of %d characters', $asserter, 40)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isSha1($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->testedInstance->setWith('z'. substr(hash('sha1', uniqid()), 1)))
			->then
				->exception(function() use ($asserter) { $asserter->isSha1(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha1)
				->mock($locale)->call('_')->withArguments('%s does not match given pattern', $asserter)->once

				->exception(function() use ($asserter) { $asserter->isSha1; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha1)
				->mock($locale)->call('_')->withArguments('%s does not match given pattern', $asserter)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isSha1($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testIsSha256()
	{
		$this
			->if($asserter = $this->newTestedInstance)

			->if($this->testedInstance->setWith(hash('sha256', uniqid())))
			->then
				->object($this->testedInstance->isSha256())->isTestedInstance
				->object($this->testedInstance->isSha256)->isTestedInstance

			->if($this->testedInstance->setWith(strtoupper(hash('sha256', uniqid()))))
			->then
				->object($this->testedInstance->isSha256())->isTestedInstance
				->object($this->testedInstance->isSha256)->isTestedInstance

			->if(
				$this->testedInstance
					->setWith(md5(uniqid()))
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notSha256 = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isSha256(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha256)
				->mock($locale)->call('_')->withArguments('%s should be a string of %d characters', $asserter, 64)->once

				->exception(function() use ($asserter) { $asserter->isSha256; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha256)
				->mock($locale)->call('_')->withArguments('%s should be a string of %d characters', $asserter, 64)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isSha256($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->testedInstance->setWith('z'. substr(hash('sha256', uniqid()), 1)))
			->then
				->exception(function() use ($asserter) { $asserter->isSha256(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha256)
				->mock($locale)->call('_')->withArguments('%s does not match given pattern', $asserter)->once

				->exception(function() use ($asserter) { $asserter->isSha256; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha256)
				->mock($locale)->call('_')->withArguments('%s does not match given pattern', $asserter)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isSha256($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testIsSha512()
	{
		$this
			->if($asserter = $this->newTestedInstance)

			->if($this->testedInstance->setWith(hash('sha512', uniqid())))
			->then
				->object($this->testedInstance->isSha512())->isTestedInstance
				->object($this->testedInstance->isSha512)->isTestedInstance

			->if($this->testedInstance->setWith(strtoupper(hash('sha512', uniqid()))))
			->then
				->object($this->testedInstance->isSha512())->isTestedInstance
				->object($this->testedInstance->isSha512)->isTestedInstance

			->if(
				$this->testedInstance
					->setWith(md5(uniqid()))
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notSha512 = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isSha512(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha512)
				->mock($locale)->call('_')->withArguments('%s should be a string of %d characters', $asserter, 128)->once

				->exception(function() use ($asserter) { $asserter->isSha512; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha512)
				->mock($locale)->call('_')->withArguments('%s should be a string of %d characters', $asserter, 128)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isSha512($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->testedInstance->setWith('z'. substr(hash('sha512', uniqid()), 1)))
			->then
				->exception(function() use ($asserter) { $asserter->isSha512(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha512)
				->mock($locale)->call('_')->withArguments('%s does not match given pattern', $asserter)->once

				->exception(function() use ($asserter) { $asserter->isSha512; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notSha512)
				->mock($locale)->call('_')->withArguments('%s does not match given pattern', $asserter)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isSha512($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}

	public function testIsMd5()
	{
		$this
			->if($asserter = $this->newTestedInstance)

			->if($this->testedInstance->setWith(hash('md5', uniqid())))
			->then
				->object($this->testedInstance->isMd5())->isTestedInstance
				->object($this->testedInstance->isMd5)->isTestedInstance

			->if($this->testedInstance->setWith(strtoupper(hash('md5', uniqid()))))
			->then
				->object($this->testedInstance->isMd5())->isTestedInstance
				->object($this->testedInstance->isMd5)->isTestedInstance

			->if(
				$this->testedInstance
					->setWith(sha1(uniqid()))
					->setLocale($locale = new \mock\atoum\locale()),
				$this->calling($locale)->_ = $notMd5 = uniqid()
			)
			->then
				->exception(function() use ($asserter) { $asserter->isMd5(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notMd5)
				->mock($locale)->call('_')->withArguments('%s should be a string of %d characters', $asserter, 32)->once

				->exception(function() use ($asserter) { $asserter->isMd5; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notMd5)
				->mock($locale)->call('_')->withArguments('%s should be a string of %d characters', $asserter, 32)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isMd5($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)

			->if($this->testedInstance->setWith('z'. substr(hash('md5', uniqid()), 1)))
			->then
				->exception(function() use ($asserter) { $asserter->isMd5(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notMd5)
				->mock($locale)->call('_')->withArguments('%s does not match given pattern', $asserter)->once

				->exception(function() use ($asserter) { $asserter->isMd5; })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($notMd5)
				->mock($locale)->call('_')->withArguments('%s does not match given pattern', $asserter)->twice

				->exception(function() use ($asserter, & $failMessage) { $asserter->isMd5($failMessage = uniqid()); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage($failMessage)
		;
	}
}
