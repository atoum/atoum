<?php

namespace mageekguy\atoum\tests\units\template;

use
	\mageekguy\atoum,
	\mageekguy\atoum\template
;

require_once(__DIR__ . '/../../runner.php');

class iterator extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass
				->hasInterface('\Countable')
				->hasInterface('\Iterator')
		;
	}

	public function test__get()
	{
		$iterator = new template\iterator();

		$this->assert
			->object($innerIterator = $iterator->{uniqid()})->isInstanceOf('\mageekguy\atoum\template\iterator')
			->sizeOf($innerIterator)->isZero()
		;

		$template = new atoum\template();
		$template->addChild($tag = new template\tag(uniqid()));

		$iterator->addTag($tag->getTag(), $template);

		$tag->addChild($childTag = new template\tag($tag->getTag()));

		$this->assert
			->object($innerIterator = $iterator->{uniqid()})->isInstanceOf('\mageekguy\atoum\template\iterator')
			->sizeOf($innerIterator)->isZero()
			->object($innerIterator = $iterator->{$childTag->getTag()})->isInstanceOf('\mageekguy\atoum\template\iterator')
			->sizeOf($innerIterator)->isEqualTo(1)
		;

		$childTag->addChild($littleChildTag = new template\tag(uniqid()));

		$this->assert
			->object($innerIterator = $iterator->{uniqid()})->isInstanceOf('\mageekguy\atoum\template\iterator')
			->sizeOf($innerIterator)->isZero()
			->object($innerIterator = $iterator->{$childTag->getTag()})->isInstanceOf('\mageekguy\atoum\template\iterator')
			->sizeOf($innerIterator)->isEqualTo(1)
			->object($innerIterator = $iterator->{$childTag->getTag()}->{$littleChildTag->getTag()})->isInstanceOf('\mageekguy\atoum\template\iterator')
			->sizeOf($innerIterator)->isEqualTo(1)
		;
	}

	public function testAddTemplate()
	{
		$iterator = new template\iterator();

		$template = new atoum\template();

		$this->assert
			->object($iterator->addTag(uniqid(), $template))->isIdenticalTo($iterator)
			->sizeOf($iterator)->isZero()
		;

		$template->addChild($tag = new template\tag(uniqid()));

		$this->assert
			->object($iterator->addTag(uniqid(), $template))->isIdenticalTo($iterator)
			->sizeOf($iterator)->isZero()
			->object($iterator->addTag($tag->getTag(), $template))->isIdenticalTo($iterator)
			->sizeOf($iterator)->isEqualTo(1)
		;

		$tag->addChild($childTag = new template\tag($tag->getTag()));

		$iterator = new template\iterator();

		$this->assert
			->object($iterator->addTag(uniqid(), $template))->isIdenticalTo($iterator)
			->sizeOf($iterator)->isZero()
			->object($iterator->addTag($tag->getTag(), $template))->isIdenticalTo($iterator)
			->sizeOf($iterator)->isEqualTo(2)
		;
	}

	public function testValid()
	{
		$iterator = new template\iterator();

		$this->assert
			->boolean($iterator->valid())->isFalse()
		;

		$template = new atoum\template();
		$template->addChild($tag = new template\tag(uniqid()));

		$iterator->addTag($tag->getTag(), $template);

		$this->assert
			->boolean($iterator->valid())->isTrue()
		;

		$iterator->next();

		$this->assert
			->boolean($iterator->valid())->isFalse()
		;
	}

	public function testRewind()
	{
		$iterator = new template\iterator();

		$this->assert
			->object($iterator->rewind())->isIdenticalTo($iterator)
		;

		$template = new atoum\template();
		$template->addChild($tag = new template\tag(uniqid()));
		$template->addChild(new template\tag($tag->getTag()));

		$iterator->addTag($tag->getTag(), $template);

		$iterator->next();

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isEqualTo(1)
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
		;

		$iterator->next()->next();

		$this->assert
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
			->object($iterator->rewind())->isIdenticalTo($iterator)
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
		;
	}

	public function testKey()
	{
		$iterator = new template\iterator();

		$this->assert
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
		;

		$template = new atoum\template();
		$template->addChild($tag = new template\tag(uniqid()));

		$iterator->addTag($tag->getTag(), $template);

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->integer($iterator->key())->isZero()
		;

		$iterator->next();

		$this->assert
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->key())->isNull()
		;
	}

	public function testCurrent()
	{
		$iterator = new template\iterator();

		$this->assert
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->current())->isNull()
		;

		$template = new atoum\template();
		$template->addChild($tag = new template\tag(uniqid()));

		$iterator->addTag($tag->getTag(), $template);

		$this->assert
			->boolean($iterator->valid())->isTrue()
			->object($iterator->current())->isIdenticalTo($tag)
		;

		$iterator->next();

		$this->assert
			->boolean($iterator->valid())->isFalse()
			->variable($iterator->current())->isNull()
		;
	}
}

?>
