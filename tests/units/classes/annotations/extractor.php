<?php

namespace mageekguy\atoum\tests\units\annotations;

use
	mageekguy\atoum,
	mageekguy\atoum\annotations
;

require_once __DIR__ . '/../../runner.php';

class extractor extends atoum\test
{
	public function testSpace()
	{
		$this->assert
			->string(self::space())->match('/ {1,10}/')
			->string(self::space(5))->match('/ {1,5}/')
			->string(self::space(5, 3))->match('/ {3,5}/')
		;
	}

	public function testStar()
	{
		$this->assert
			->string(self::star())->match('/\*{2,10}/')
			->string(self::star(5))->match('/\*{2,5}/')
			->string(self::star(5, 3))->match('/\*{3,5}/')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($extractor = new annotations\extractor())
			->then
				->array($extractor->getHandlers())->isEmpty()
		;
	}

	public function testExtract()
	{
		$this->assert
			->if($extractor = new annotations\extractor())
			->and($extractor->setHandler('ignore', function($value) use (& $ignore) { $ignore = $value; }))
			->and($extractor->setHandler('tags', function($value) use (& $tags) { $tags = $value; }))
			->and($extractor->setHandler('dataProvider', function($value) use (& $dataProvider) { $dataProvider = $value; }))
			->and($extractor->setHandler('namespace', function($value) use (& $namespace) { $namespace = $value; }))
			->and($extractor->setHandler('maxChildrenNumber', function($value) use (& $maxChildrenNumber) { $maxChildrenNumber = $value; }))
			->then
				->object($extractor->extract(''))->isIdenticalTo($extractor)
					->variable($ignore)->isNull()
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract(uniqid()))->isIdenticalTo($extractor)
					->variable($ignore)->isNull()
					->variable($tags)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** */'))->isIdenticalTo($extractor)
					->variable($ignore)->isNull()
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore on */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('on')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore ON */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('ON')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore On */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('On')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore oN */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('oN')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore oNo */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('oNo')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore Off */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('Off')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @IGNORE off */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('off')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @tags aTag */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag')
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @tags aTag otherTag anotherTag */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag otherTag anotherTag')
					->variable($dataProvider)->isNull()
				->object($extractor->extract('/** @dataProvider aDataProvider */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag otherTag anotherTag')
					->string($dataProvider)->isEqualTo('aDataProvider')
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @DATApROVIDER aDataProvider */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag otherTag anotherTag')
					->string($dataProvider)->isEqualTo('aDataProvider')
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @namespace bar */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag otherTag anotherTag')
					->string($dataProvider)->isEqualTo('aDataProvider')
					->string($namespace)->isEqualTo('bar')
				->object($extractor->extract('/** @foo bar */'))->isIdenticalTo($extractor)
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag otherTag anotherTag')
					->string($dataProvider)->isEqualTo('aDataProvider')
					->string($namespace)->isEqualTo('bar')
				->object($extractor->extract('/** @maxChildrenNumber 1 */'))->isIdenticalTo($extractor)
					->string($maxChildrenNumber)->isEqualTo('1')
				->object($extractor->extract('/** @maxChildrenNumber ' . ($number = rand(1, PHP_INT_MAX)) . ' */'))->isIdenticalTo($extractor)
					->string($maxChildrenNumber)->isEqualTo($number)
		;
	}

	public function testResetHandlers()
	{
		$this->assert
			->if($extractor = new annotations\extractor())
			->then
				->object($extractor->resetHandlers())->isIdenticalTo($extractor)
				->array($extractor->getHandlers())->isEmpty()
			->if($extractor->setHandler(uniqid(), function() {}))
			->then
				->object($extractor->resetHandlers())->isIdenticalTo($extractor)
				->array($extractor->getHandlers())->isEmpty()
		;
	}

	public function testUnsetHandler()
	{
		$this->assert
			->if($extractor = new annotations\extractor())
			->then
				->object($extractor->unsetHandler(uniqid()))->isIdenticalTo($extractor)
				->array($extractor->getHandlers())->isEmpty()
			->if($extractor->setHandler($annotation = uniqid(), function() {}))
			->then
				->object($extractor->unsetHandler(uniqid()))->isIdenticalTo($extractor)
				->array($extractor->getHandlers())->isNotEmpty()
				->object($extractor->unsetHandler($annotation))->isIdenticalTo($extractor)
				->array($extractor->getHandlers())->isEmpty()
		;
	}

	public function testToBoolean()
	{
		$this->assert
			->boolean(annotations\extractor::toBoolean('on'))->isTrue()
			->boolean(annotations\extractor::toBoolean('On'))->isTrue()
			->boolean(annotations\extractor::toBoolean('ON'))->isTrue()
			->boolean(annotations\extractor::toBoolean('oN'))->isTrue()
			->boolean(annotations\extractor::toBoolean('off'))->isFalse()
			->boolean(annotations\extractor::toBoolean('Off'))->isFalse()
			->boolean(annotations\extractor::toBoolean('OFF'))->isFalse()
		;
	}

	public function testToArray()
	{
		$this->assert
			->array(annotations\extractor::toArray(''))->isEqualTo(array(''))
			->array(annotations\extractor::toArray($value = uniqid()))->isEqualTo(array($value))
			->array(annotations\extractor::toArray(($value = uniqid()) . ' ' . ($otherValue = uniqid())))->isEqualTo(array($value, $otherValue))
		;
	}

	protected static function repeat($char, $max, $min = 1)
	{
		return str_repeat($char, rand($min, rand($min, $max)));
	}

	protected static function space($max = 10, $min = 1)
	{
		return self::repeat(' ', $max, $min);
	}

	protected static function star($max = 10, $min = 2)
	{
		return self::repeat('*', $max, $min);
	}
}
