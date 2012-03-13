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

	public function testExtract()
	{
		$extractor = new annotations\extractor();

		$this->assert
			->object($extractor->extract(''))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isEmpty()
			->object($extractor->extract(uniqid()))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isEmpty()
			->object($extractor->extract('/** */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isEmpty()
			->object($extractor->extract('/** @ignore on */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => true))
			->object($extractor->extract('/** @ignore ON */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => true))
			->object($extractor->extract('/** @ignore On */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => true))
			->object($extractor->extract('/** @ignore oN */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => true))
			->object($extractor->extract('/** @ignore oNo */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => false))
			->object($extractor->extract('/** @ignore off */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isIdenticalTo(array('ignore' => false))
			->object($extractor->extract('/** @tags aTag */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isIdenticalTo(array('tags' => array('aTag')))
			->object($extractor->extract('/** @tags aTag otherTag anotherTag */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isIdenticalTo(array('tags' => array('aTag', 'otherTag', 'anotherTag')))
			->object($extractor->extract('/** @dataProvider aDataProvider */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isIdenticalTo(array('dataProvider' => 'aDataProvider'))
			->object($extractor->extract('/** @DATApROVIDER aDataProvider */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isIdenticalTo(array('dataProvider' => 'aDataProvider'))
			->object($extractor->extract('/** @namespace bar */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isEqualTo(array('namespace' => 'bar'))
			->object($extractor->extract('/** @foo bar */'))->isIdenticalTo($extractor)
			->array($extractor->getAnnotations())->isEmpty()
		;
	}

	public function testSetTest()
	{
		$extractor = new annotations\extractor();

		$this->assert
			->object($extractor->setTest($test = new self(), ''))->isIdenticalTo($extractor)
			->boolean($test->isIgnored())->isFalse()
			->array($test->getAllTags())->isEmpty()
			->object($extractor->setTest($test = new self(), '/** @ignore on */'))->isIdenticalTo($extractor)
			->boolean($test->isIgnored())->isTrue()
			->array($test->getTags())->isEmpty()
			->object($extractor->setTest($test = new self(), '/** @tags aTag otherTag anotherTag */'))->isIdenticalTo($extractor)
			->boolean($test->isIgnored())->isFalse()
			->array($test->getTags())->isEqualTo(array('aTag', 'otherTag', 'anotherTag'))
			->object($extractor->setTest($test = new self(), '/** @namespace bar */'))->isIdenticalTo($extractor)
			->string($test->getTestNamespace())->isEqualTo('bar')
			->object($extractor->setTest($test = new self(), '/** @namespace \bar\ */'))->isIdenticalTo($extractor)
			->string($test->getTestNamespace())->isEqualTo('bar')
		;
	}

	public function testSetTestMethod()
	{
		$extractor = new annotations\extractor();

		$this->assert
			->object($extractor->setTestMethod($test = new self(), __FUNCTION__, ''))->isIdenticalTo($extractor)
			->boolean($test->methodIsIgnored(__FUNCTION__))->isFalse()
			->array($test->getMethodTags(__FUNCTION__))->isEmpty()
			->object($extractor->setTestMethod($test = new self(), __FUNCTION__, '/** @ignore on */'))->isIdenticalTo($extractor)
			->boolean($test->methodIsIgnored(__FUNCTION__))->isTrue()
			->array($test->getMethodTags(__FUNCTION__))->isEmpty()
			->object($extractor->setTestMethod($test = new self(), __FUNCTION__, '/** @ignore off */'))->isIdenticalTo($extractor)
			->boolean($test->methodIsIgnored(__FUNCTION__))->isFalse()
			->array($test->getMethodTags(__FUNCTION__))->isEmpty()
			->object($extractor->setTestMethod($test = new self(), __FUNCTION__, '/** @tags aTag otherTag anotherTag */'))->isIdenticalTo($extractor)
			->boolean($test->methodIsIgnored(__FUNCTION__))->isFalse()
			->array($test->getMethodTags(__FUNCTION__))->isEqualTo(array('aTag', 'otherTag', 'anotherTag'))
			->object($extractor->setTestMethod($test = new self(), __FUNCTION__, '/** @tags aTag otherTag anotherTag' . PHP_EOL . '@ignore on */'))->isIdenticalTo($extractor)
			->boolean($test->methodIsIgnored(__FUNCTION__))->isTrue()
			->array($test->getMethodTags(__FUNCTION__))->isEqualTo(array('aTag', 'otherTag', 'anotherTag'))
		;
	}
}

?>
