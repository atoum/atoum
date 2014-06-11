<?php

namespace mageekguy\atoum\tests\units\template;

use
	mageekguy\atoum,
	mageekguy\atoum\template
;

require_once __DIR__ . '/../../runner.php';

class tag extends atoum\test
{
	public function test__construct()
	{
		$this
			->testedClass
				->isSubClassOf('mageekguy\atoum\template')
			->exception(function() {
					$template = new template\tag('');
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Tag must not be an empty string')
			->exception(function() {
					$template = new template\tag(uniqid(), null, 0);
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Line must be greater than 0')
			->exception(function() {
						$template = new template\tag(uniqid(), null, - rand(1, PHP_INT_MAX));
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Line must be greater than 0')
			->exception(function() {
						$template = new template\tag(uniqid(), null, rand(1, PHP_INT_MAX), 0);
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Offset must be greater than 0')
			->exception(function() {
					$template = new template\tag(uniqid(), null, rand(1, PHP_INT_MAX), - rand(1, PHP_INT_MAX));
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic')
				->hasMessage('Offset must be greater than 0')
			->if($template = new template\tag($tag = uniqid()))
			->then
				->string($template->getTag())->isEqualTo($tag)
				->string($template->getData())->isEmpty()
				->variable($template->getLine())->isNull()
				->variable($template->getOffset())->isNull()
			->if($template = new template\tag($tag = uniqid(), $data = uniqid(), $line = rand(1, PHP_INT_MAX), $offset = rand(1, PHP_INT_MAX)))
			->then
				->string($template->getTag())->isEqualTo($tag)
				->string($template->getData())->isEqualTo($data)
				->integer($template->getLine())->isEqualTo($line)
				->integer($template->getOffset())->isEqualTo($offset)
		;
	}

	public function testSetId()
	{
		$this
			->if($template = new template\tag(uniqid()))
			->then
				->exception(function() use ($template) {
							$template->setId('');
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Id must not be empty')
				->object($template->setId($id = uniqid()))->isIdenticalTo($template)
				->string($template->getId())->isEqualTo($id)
				->object($template->setId($id = uniqid()))->isIdenticalTo($template)
				->string($template->getId())->isEqualTo($id)
			->if($root = new template\tag(uniqid()))
			->and($root->setId($id = uniqid()))
			->and($root->addChild($template = new template\tag(uniqid())))
			->then
				->exception(function() use ($template, $id) {
							$template->setId($id);
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Id \'' . $id . '\' is already defined in line unknown at offset unknown')
		;
	}

	public function testUnsetId()
	{
		$this
			->if($template = new template\tag(uniqid()))
			->then
				->variable($template->getId())->isNull()
				->object($template->unsetId())->isIdenticalTo($template)
				->variable($template->getId())->isNull()
			->if($template->setId(uniqid()))
			->then
				->variable($template->getId())->isNotNull()
				->object($template->unsetId())->isIdenticalTo($template)
				->variable($template->getId())->isNull()
		;
	}

	public function testSetAttribute()
	{
		$this
			->if($template = new template\tag(uniqid()))
			->then
				->variable($template->getId())->isNull()
				->object($template->setAttribute('id', $id = uniqid()))->isIdenticalTo($template)
				->string($template->getId())->isEqualTo($id)
				->exception(function() use ($template, & $attribute) {
							$template->setAttribute($attribute = uniqid(), uniqid());
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Attribute \'' . $attribute . '\' is unknown')
		;
	}

	public function testUnsetAttribute()
	{
		$this
			->if($template = new template\tag(uniqid()))
			->and($template->setAttribute('id', $id = uniqid()))
			->then
				->string($template->getId())->isEqualTo($id)
				->object($template->unsetAttribute('id'))->isIdenticalTo($template)
				->variable($template->getId())->isNull()
				->exception(function() use ($template, & $attribute) {
							$template->unsetAttribute($attribute = uniqid());
						}
					)
					->isInstanceOf('mageekguy\atoum\exceptions\logic')
					->hasMessage('Attribute \'' . $attribute . '\' is unknown')
		;
	}
}
