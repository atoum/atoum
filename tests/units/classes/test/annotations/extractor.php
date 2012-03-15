<?php

namespace mageekguy\atoum\tests\units\test\annotations;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\test\annotations

;

class extractor extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->hasParent('mageekguy\atoum\annotations\extractor')
		;
	}

	public function test__construct()
	{
		$this->assert
			->if($extractor = new annotations\extractor())
			->then
				->array($extractor->getAnnotations())->isEmpty()
				->array($extractor->getHandlers())->isEmpty()
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

	public function testExtract()
	{
		$this->assert
			->if($extractor = new annotations\extractor())
			->and($extractor->setHandler('ignore', function($value) use (& $ignore) { $ignore = $value; }))
			->and($extractor->setHandler('tags', function($value) use (& $tags) { $tags = $value; }))
			->and($extractor->setHandler('dataProvider', function($value) use (& $dataProvider) { $dataProvider = $value; }))
			->and($extractor->setHandler('namespace', function($value) use (& $namespace) { $namespace = $value; }))
			->then
				->object($extractor->extract(''))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isEmpty()
					->variable($ignore)->isNull()
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract(uniqid()))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isEmpty()
					->variable($ignore)->isNull()
					->variable($tags)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isEmpty()
					->variable($ignore)->isNull()
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore on */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => 'on'))
					->string($ignore)->isEqualTo('on')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore ON */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => 'ON'))
					->string($ignore)->isEqualTo('ON')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore On */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => 'On'))
					->string($ignore)->isEqualTo('On')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore oN */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => 'oN'))
					->string($ignore)->isEqualTo('oN')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore oNo */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => 'oNo'))
					->string($ignore)->isEqualTo('oNo')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @ignore Off */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => 'Off'))
					->string($ignore)->isEqualTo('Off')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @IGNORE off */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('IGNORE' => 'off'))
					->string($ignore)->isEqualTo('off')
					->variable($tags)->isNull()
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @tags aTag */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('tags' => 'aTag'))
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag')
					->variable($dataProvider)->isNull()
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @tags aTag otherTag anotherTag */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('tags' => 'aTag otherTag anotherTag'))
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag otherTag anotherTag')
					->variable($dataProvider)->isNull()
				->object($extractor->extract('/** @dataProvider aDataProvider */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('dataProvider' => 'aDataProvider'))
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag otherTag anotherTag')
					->string($dataProvider)->isEqualTo('aDataProvider')
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @DATApROVIDER aDataProvider */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isIdenticalTo(array('DATApROVIDER' => 'aDataProvider'))
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag otherTag anotherTag')
					->string($dataProvider)->isEqualTo('aDataProvider')
					->variable($namespace)->isNull()
				->object($extractor->extract('/** @namespace bar */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isEqualTo(array('namespace' => 'bar'))
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag otherTag anotherTag')
					->string($dataProvider)->isEqualTo('aDataProvider')
					->string($namespace)->isEqualTo('bar')
				->object($extractor->extract('/** @foo bar */'))->isIdenticalTo($extractor)
					->array($extractor->getAnnotations())->isEqualTo(array('foo' => 'bar'))
					->string($ignore)->isEqualTo('off')
					->string($tags)->isEqualTo('aTag otherTag anotherTag')
					->string($dataProvider)->isEqualTo('aDataProvider')
					->string($namespace)->isEqualTo('bar')
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
}

?>
