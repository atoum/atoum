<?php

namespace mageekguy\atoum\tests\units\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\tools\diffs
;

require_once __DIR__ . '/../../runner.php';

class hash extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubclassOf('mageekguy\atoum\asserters\string');
	}

	public function testIsSha1()
	{
		$this
			->if($asserter = new asserters\hash($generator = new asserter\generator()))
			->and($asserter->setWith($value = hash('sha1', 'hello')))
			->then
				->object($asserter->isSha1())->isIdenticalTo($asserter)
			->if($asserter->setWith($newvalue = substr($value, 1)))
			->and($diff = new diffs\variable())
			->and($diff->setReference( $newvalue )->setData($value))
			->then
				->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha1(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value)))
			->if($asserter->setWith($newvalue = 'z'.substr($value, 1) ))
			->and($diff = new diffs\variable())
			->and($diff->setReference($newvalue)->setData($value))
			->then
				->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha1(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not match given pattern'), $asserter))
		;
	}

	public function testIsSha256()
	{
		$this
			->if($asserter = new asserters\hash($generator = new asserter\generator()))
			->and($asserter->setWith($value = hash('sha256', 'hello')))
			->then
				->object($asserter->isSha256())->isIdenticalTo($asserter)
			->if($asserter->setWith($newvalue = substr($value, 1)))
			->and($diff = new diffs\variable())
			->and($diff->setReference( $newvalue )->setData($value))
			->then
				->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha256(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value)))
			->if($asserter->setWith($newvalue = 'z'.substr($value, 1) ))
			->and($diff = new diffs\variable())
			->and($diff->setReference($newvalue)->setData($value))
			->then
				->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha256(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not match given pattern'), $asserter))
		;
	}

	public function testIsSha512()
	{
		$this
			->if($asserter = new asserters\hash($generator = new asserter\generator()))
			->and($asserter->setWith($value = hash('sha512', 'hello')))
			->then
				->object($asserter->isSha512())->isIdenticalTo($asserter)
			->if($asserter->setWith($newvalue = substr($value, 1)))
			->and($diff = new diffs\variable())
			->and($diff->setReference( $newvalue )->setData($value))
			->then
				->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha512(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value)))
			->if($asserter->setWith($newvalue = 'z'.substr($value, 1) ))
			->and($diff = new diffs\variable())
			->and($diff->setReference($newvalue)->setData($value))
			->then
				->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isSha512(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not match given pattern'), $asserter))
		;
	}

	public function testIsMd5()
	{
		$this
			->if($asserter = new asserters\hash($generator = new asserter\generator()))
			->and($asserter->setWith($value = hash('md5', 'hello')))
			->then
				->object($asserter->isMd5())->isIdenticalTo($asserter)
			->if($asserter->setWith($newvalue = substr($value, 1)))
			->and($diff = new diffs\variable())
			->and($diff->setReference( $newvalue )->setData($value))
			->then
				->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isMd5(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($this->getLocale()->_('%s should be a string of %d characters'), $asserter, strlen($value)))
			->if($asserter->setWith($newvalue = 'z'.substr($value, 1) ))
			->and($diff = new diffs\variable())
			->and($diff->setReference($newvalue)->setData($value))
			->then
				->exception(function() use ($asserter, & $line) { $line = __LINE__; $asserter->isMd5(); })
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf($generator->getLocale()->_('%s does not match given pattern'), $asserter))
		;
	}
}
