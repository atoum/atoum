<?php

namespace mageekguy\atoum\tests\units\template;

use
	\mageekguy\atoum,
	\mageekguy\atoum\template
;

require_once(__DIR__ . '/../../runner.php');

class parser extends atoum\test
{
	public function testClass()
	{
		$this->assert
			->testedClass->hasInterface('\mageekguy\atoum\adapter\aggregator')
		;
	}

	public function test__construct()
	{
		$parser = new template\parser();

		$this->assert
			->string($parser->getNamespace())->isEqualTo(template\parser::defaultNamespace)
			->object($parser->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
		;

		$parser = new template\parser($namespace = uniqid());

		$this->assert
			->string($namespace)->isEqualTo($parser->getNamespace())
			->object($parser->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
		;

		$parser = new template\parser($namespace = rand(1, PHP_INT_MAX));

		$this->assert
			->string($parser->getNamespace())->isEqualTo((string) $namespace)
			->object($parser->getAdapter())->isInstanceOf('\mageekguy\atoum\adapter')
		;

		$parser = new template\parser($namespace = uniqid(), $adapter = new atoum\test\adapter());

		$this->assert
			->string($parser->getNamespace())->isEqualTo((string) $namespace)
			->object($parser->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetNamespace()
	{
		$parser = new template\parser();

		$this->assert
			->object($parser->setNamespace($namespace = uniqid()))->isIdenticalTo($parser)
			->string($parser->getNamespace())->isEqualTo($namespace)
		;

		$this->assert
			->object($parser->setNamespace($namespace = rand(1, PHP_INT_MAX)))->isIdenticalTo($parser)
			->string($parser->getNamespace())->isEqualTo((string) $namespace)
		;
	}

	public function testSetAdapter()
	{
		$parser = new template\parser();

		$this->assert
			->object($parser->setAdapter($adapter = new atoum\test\adapter()))->isIdenticalTo($parser)
			->object($parser->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testParseString()
	{
		$parser = new template\parser();

		$this->assert
			->object($root = $parser->parseString(''))->isInstanceOf('\mageekguy\atoum\template')
			->array($root->getChildren())->isEmpty()
		;

		$this->assert
			->object($root = $parser->parseString($string = uniqid()))->isInstanceOf('\mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('\mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string)
		;

		$this->assert
			->object($root = $parser->parseString($string = uniqid() . "\n" . uniqid() . "\n"))->isInstanceOf('\mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('\mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string)
		;

		$this->assert
			->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' />'))->isInstanceOf('\mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('\mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->variable($root->getChild(0)->getId())->isNull()
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>'))->isInstanceOf('\mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('\mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->variable($root->getChild(0)->getId())->isNull()
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '" />'))->isInstanceOf('\mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('\mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->string($root->getChild(0)->getId())->isEqualTo($id)
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '"/>'))->isInstanceOf('\mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('\mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->string($root->getChild(0)->getId())->isEqualTo($id)
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '>' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>'))->isInstanceOf('\mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('\mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->variable($root->getChild(0)->getId())->isNull()
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->object($root = $parser->parseString(($string1 = uniqid()) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '>' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>' . ($string2 = uniqid())))->isInstanceOf('\mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(3)
			->object($root->getChild(0))->isInstanceOf('\mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string1)
			->object($root->getChild(1))->isInstanceOf('\mageekguy\atoum\template\tag')
			->variable($root->getChild(1)->getId())->isNull()
			->string($root->getChild(1)->getData())->isEmpty()
			->integer($root->getChild(1)->getLine())->isEqualTo(1)
			->integer($root->getChild(1)->getOffset())->isEqualTo(strlen($string1) + 1)
			->object($root->getChild(2))->isInstanceOf('\mageekguy\atoum\template\data')
			->string($root->getChild(2)->getData())->isEqualTo($string2)
		;

		$this->assert
			->object($root = $parser->parseString(($string1 = uniqid()) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '">' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>' . ($string2 = uniqid())))->isInstanceOf('\mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(3)
			->object($root->getChild(0))->isInstanceOf('\mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string1)
			->object($root->getChild(1))->isInstanceOf('\mageekguy\atoum\template\tag')
			->string($root->getChild(1)->getId())->isEqualTo($id)
			->string($root->getChild(1)->getData())->isEmpty()
			->integer($root->getChild(1)->getLine())->isEqualTo(1)
			->integer($root->getChild(1)->getOffset())->isEqualTo(strlen($string1) + 1)
			->object($root->getChild(2))->isInstanceOf('\mageekguy\atoum\template\data')
			->string($root->getChild(2)->getData())->isEqualTo($string2)
		;

		$this->assert
			->object($root = $parser->parseString(($string = str_repeat("\n", 6)) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>'))
			->array($root->getChildren())->hasSize(2)
			->object($root->getChild(0))->isInstanceOf('\mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string)
			->object($root->getChild(1))->isInstanceOf('\mageekguy\atoum\template\tag')
			->variable($root->getChild(1)->getId())->isNull()
			->string($root->getChild(1)->getData())->isEmpty()
			->integer($root->getChild(1)->getLine())->isEqualTo(7)
			->integer($root->getChild(1)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->exception(function() use ($parser) {
					$parser->parseString('<' . template\parser::defaultNamespace . ':' . uniqid() . ' id="' . ($id = uniqid()) . '">' . "\n" . '<' . template\parser::defaultNamespace . ':' . uniqid() . ' id="' . $id . '">');
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
		;
	}

	public function testCheckString()
	{
		$this->define->parserException = '\mageekguy\atoum\tests\units\asserters\template\parser\exception';

		$parser = new template\parser();

		$this->assert
			->object($parser->checkString(uniqid()))->isIdenticalTo($parser)
			->object($parser->checkString('<' . uniqid() . ':' . uniqid() . ' />'))->isIdenticalTo($parser)
		;

		$tag = uniqid();

		$this->assert
			->object($parser->checkString('<'))->isIdenticalTo($parser)
			->object($parser->checkString('<' . template\parser::defaultNamespace))->isIdenticalTo($parser)
			->object($parser->checkString('<' . template\parser::defaultNamespace . ':'))->isIdenticalTo($parser)
			->object($parser->checkString('<' . template\parser::defaultNamespace . ':' . $tag))->isIdenticalTo($parser)
			->object($parser->checkString('<' . template\parser::defaultNamespace . ':' . $tag . '/>'))->isIdenticalTo($parser)
			->parserException(function() use ($parser, $tag) {
					$parser->checkString('<' . template\parser::defaultNamespace . ':' . $tag . ' id="" />');
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('\mageekguy\atoum\template\parser\exception')
				->hasMessage('Id must not be empty')
				->hasErrorLine(1)
				->hasErrorOffset(1)
			->object($parser->checkString('<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . uniqid() . '" />'))->isIdenticalTo($parser)
			->object($parser->checkString('<' . template\parser::defaultNamespace . ':' . $tag . '></' . template\parser::defaultNamespace . ':' . $tag . '>'))->isIdenticalTo($parser)
			->object($parser->checkString('<' . template\parser::defaultNamespace . ':' . $tag . '   ' . "\t" . '   ></' . template\parser::defaultNamespace . ':' . $tag . '>'))->isIdenticalTo($parser)
			->object($parser->checkString('<' . template\parser::defaultNamespace . ':' . $tag . '></' . template\parser::defaultNamespace . ':' . $tag . '  ' . "\t" . '    >'))->isIdenticalTo($parser)
		;

		$tagWithId = '<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . ($id = uniqid()) . '" />';

		$this->assert
			->parserException(function() use ($parser, $tagWithId) {
					$parser->checkString($tagWithId . $tagWithId);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('\mageekguy\atoum\template\parser\exception')
				->hasMessage('Id \'' . $id . '\' is already defined in line 1 at offset 1')
				->hasErrorLine(1)
				->hasErrorOffset(41)
		;

		$tagWithInvalidAttribute = '<' . template\parser::defaultNamespace . ':' . $tag . ' foo="bar" />';

		$this->assert
			->parserException(function() use ($parser, $tagWithInvalidAttribute) {
					$parser->checkString($tagWithInvalidAttribute);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('\mageekguy\atoum\template\parser\exception')
				->hasMessage('Attribute \'foo\' is unknown')
				->hasErrorLine(1)
				->hasErrorOffset(1)
		;

		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>';

		$this->assert
			->parserException(function() use ($parser, $firstTag) {
					$parser->checkString($firstTag);
				}
			)
				->isInstanceOf('\mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('\mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $tag . '\' must be closed')
				->hasErrorLine(1)
				->hasErrorOffset(20)
		;

		/*
		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>';
		$this->assert
			->boolean($parser->checkString("\n\n\n\n" . $firstTag, $error))->isFalse()
			->and
			->string($error)->isEqualTo('Line 5 at offset ' . (strlen($firstTag) + 1) . ' : Tag \'' . $tag . '\' must be closed')
		;

		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>';
		$this->assert
			->boolean($parser->checkString("\n\n\n\n    " . $firstTag, $error))->isFalse()
			->and
			->string($error)->isEqualTo('Line 5 at offset ' . (strlen($firstTag) + 5) . ' : Tag \'' . $tag . '\' must be closed')
		;

		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>';
		$notOpenTag = uniqid();
		$secondTag = '</' . template\parser::defaultNamespace . ':' . $notOpenTag . '  ' . "\t" . '    >';
		$this->assert
			->boolean($parser->checkString($firstTag . $secondTag, $error))->isFalse()
			->and
			->string($error)->isEqualTo('Line 1 at offset ' . (strlen($firstTag) + 1) . ' : Tag \'' . $notOpenTag . '\' is not open')
		;

		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>';
		$notOpenTag = uniqid();
		$secondTag = '</' . template\parser::defaultNamespace . ':' . $notOpenTag . '  ' . "\t" . '    >';
		$this->assert
			->boolean($parser->checkString("\n\n\n\n" . $firstTag . $secondTag, $error))->isFalse()
			->and
			->string($error)->isEqualTo('Line 5 at offset ' . (strlen($firstTag) + 1) . ' : Tag \'' . $notOpenTag . '\' is not open')
		;

		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>';
		$notOpenTag = uniqid();
		$secondTag = '</' . template\parser::defaultNamespace . ':' . $notOpenTag . '  ' . "\t" . '    >';
		$this->assert
			->boolean($parser->checkString("\n\n\n\n    " . $firstTag . $secondTag, $error))->isFalse()
			->and
			->string($error)->isEqualTo('Line 5 at offset ' . (strlen($firstTag) + 5) . ' : Tag \'' . $notOpenTag . '\' is not open')
		;
		*/
	}

	/*
	public function testParseString()
	{
		$parser = new template\parser();

		# String is invalid
		$invalidArguments = array
			(
				1,
				-1,
				1.0,
				-1,0,
				true,
				false
			);

		foreach ($invalidArguments as $argument)
		{
			$parser->parseString($argument);
			$this->assert->error(E_USER_ERROR, 'Argument must be a string')->exists();
		}

		# Only one line of data
		$string = uniqid();

		$ogoHtmlTemplateRoot = $parser->parseString($string);

		$this->assert->object($ogoHtmlTemplateRoot)->isInstanceOf('ogoHtmlTemplateRoot')
			->and
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEqualTo($string)
			->and
			->string($ogoHtmlTemplateRoot->getData())->isEmpty()
		;

		# Several lines of data
		$string = uniqid() . "\n" . uniqid() . "\n" . uniqid() . "\n" . uniqid() . "\n" . uniqid() . "\n";

		$ogoHtmlTemplateRoot = $parser->parseString($string);

		$this->assert
			->object($ogoHtmlTemplateRoot)->isInstanceOf('ogoHtmlTemplateRoot')
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEqualTo($string)
		;

		# String with only one tag with no attributes, with space at end
		$tag = uniqid();
		$string = '<' . template\parser::defaultNamespace . ':' . $tag . ' />';

		$ogoHtmlTemplateRoot = $parser->parseString($string);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(0)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsEnabled())->isFalse()
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
		;

		# String with only one tag with no attributes, with no space at end
		$tag = uniqid();
		$string = '<' . template\parser::defaultNamespace . ':' . $tag . '/>';

		$ogoHtmlTemplateRoot = $parser->parseString($string);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(0)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsEnabled())->isFalse()
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
		;

		# String with only one tag with attributes, with space at end
		$tag = uniqid();
		$id = uniqid();
		$string = '<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . $id . '" html="true" />';

		$ogoHtmlTemplateRoot = $parser->parseString($string);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag)
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getId())->isEqualTo($id)
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsEnabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
		;

		# String with only one tag with attributes, with no space at end
		$tag = uniqid();
		$id = uniqid();
		$string = '<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . $id . '" html="true"/>';

		$ogoHtmlTemplateRoot = $parser->parseString($string);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag)
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getId())->isEqualTo($id)
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsEnabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEmpty()
			->and
			->string($ogoHtmlTemplateRoot->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
		;

		# String with only one tag with no attributes and data
		$tag = uniqid();
		$data = uniqid();
		$string = '<' . template\parser::defaultNamespace . ':' . $tag . '>' . $data . '</' . template\parser::defaultNamespace . ':' . $tag . '>';

		$ogoHtmlTemplateRoot = $parser->parseString($string);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(0)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsEnabled())->isFalse()
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0)->getChild(0))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->getData())->isEqualTo($data)
		;

		# String with one tag with no attributes between two string
		$tag = uniqid();
		$string0 = uniqid();
		$string1 = uniqid();
		$string = $string0 . '<' . template\parser::defaultNamespace . ':' . $tag . ' />' . $string1;

		$ogoHtmlTemplateRoot = $parser->parseString($string);

		$this->assert
			->object($ogoHtmlTemplateRoot)->isInstanceOf('ogoHtmlTemplateRoot')
			->and
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(3)
			->and
			->string($ogoHtmlTemplateRoot->getData())->isEmpty()
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEqualTo($string0)
			->and
			->object($ogoHtmlTemplateRoot->getChild(1))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getTag())->isEqualTo($tag)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(1)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(1)->htmlIsDisabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getLine())->isEqualTo(1)
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getOffset())->isEqualTo(strlen($string0) + 1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(2))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(2)->getData())->isEqualTo($string1)
		;

		# String with one tag with attributes between two string
		$tag = uniqid();
		$id = uniqid();
		$string0 = uniqid();
		$string1 = uniqid();
		$string = $string0 . '<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . $id . '" html="true" />' . $string1;

		$ogoHtmlTemplateRoot = $parser->parseString($string);

		$this->assert
			->object($ogoHtmlTemplateRoot)->isInstanceOf('ogoHtmlTemplateRoot')
			->and
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(3)
			->and
			->string($ogoHtmlTemplateRoot->getData())->isEmpty()
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEqualTo($string0)
			->and
			->object($ogoHtmlTemplateRoot->getChild(1))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getTag())->isEqualTo($tag)
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getId())->isEqualTo($id)
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(1)->htmlIsEnabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getOffset())->isEqualTo(strlen($string0) + 1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(2))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(2)->getData())->isEqualTo($string1)
		;

		# String with one tag with no attributes at end of several lines
		$string = uniqid() . "\n" . uniqid() . "\n" . uniqid() . "\n" . uniqid() . "\n" . uniqid() . "\n";
		$tag = uniqid();

		$ogoHtmlTemplateRoot = $parser->parseString($string . '<' . template\parser::defaultNamespace . ':' . $tag . ' />');

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(2)
			->and
			->object($ogoHtmlTemplateRoot)->isInstanceOf('ogoHtmlTemplateRoot')
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEqualTo($string)
			->and
			->object($ogoHtmlTemplateRoot->getChild(1))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getTag())->isEqualTo($tag)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(1)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(1)->htmlIsDisabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getLine())->isEqualTo(6)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getOffset())->isEqualTo(1)
		;

		# String with one tag with attributes at end of several lines
		$string = uniqid() . "\n" . uniqid() . "\n" . uniqid() . "\n" . uniqid() . "\n" . uniqid() . "\n";
		$tag = uniqid();
		$id = uniqid();

		$ogoHtmlTemplateRoot = $parser->parseString($string . '<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . $id . '" html="true" />');

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(2)
			->and
			->object($ogoHtmlTemplateRoot)->isInstanceOf('ogoHtmlTemplateRoot')
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEqualTo($string)
			->and
			->object($ogoHtmlTemplateRoot->getChild(1))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getTag())->isEqualTo($tag)
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getId())->isEqualTo($id)
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(1)->htmlIsEnabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getLine())->isEqualTo(6)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getOffset())->isEqualTo(1)
		;

		# String with no end of line and just tags with no attributes
		$tag1 = uniqid();
		$tag2 = uniqid();
		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag1 . ' />';
		$secondTag = '<' . template\parser::defaultNamespace . ':' . $tag2 . ' />';

		$ogoHtmlTemplateRoot = $parser->parseString($firstTag . $secondTag);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(2)
			->and
			->object($ogoHtmlTemplateRoot)->isInstanceOf('ogoHtmlTemplateRoot')
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag1)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(0)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsDisabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(1))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getTag())->isEqualTo($tag2)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(1)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(1)->htmlIsDisabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getOffset())->isEqualTo(strlen($firstTag) + 1)
		;

		# String with no end of line and just tags with attributes
		$tag1 = uniqid();
		$id1 = uniqid();
		$tag2 = uniqid();
		$id2 = uniqid();
		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag1 . ' id="' . $id1 . '" html="true" />';
		$secondTag = '<' . template\parser::defaultNamespace . ':' . $tag2 . ' id="' . $id2 . '" html="true" />';

		$ogoHtmlTemplateRoot = $parser->parseString($firstTag . $secondTag);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(2)
			->and
			->object($ogoHtmlTemplateRoot)->isInstanceOf('ogoHtmlTemplateRoot')
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag1)
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getId())->isEqualTo($id1)
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsEnabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(1))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getTag())->isEqualTo($tag2)
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getId())->isEqualTo($id2)
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(1)->htmlIsEnabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(1)->getOffset())->isEqualTo(strlen($firstTag) + 1)
		;

		# String with end of line and just tags with no attributes
		$tag1 = uniqid();
		$tag2 = uniqid();
		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag1 . ' />';
		$secondTag = '<' . template\parser::defaultNamespace . ':' . $tag2 . ' />';

		$ogoHtmlTemplateRoot = $parser->parseString($firstTag . template\parser::eol . $secondTag);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(3)
			->and
			->object($ogoHtmlTemplateRoot)->isInstanceOf('ogoHtmlTemplateRoot')
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag1)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(0)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsDisabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(1))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getData())->isEqualTo(template\parser::eol)
			->and
			->object($ogoHtmlTemplateRoot->getChild(2))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(2)->getTag())->isEqualTo($tag2)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(2)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(2)->htmlIsDisabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(2)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(2)->getLine())->isEqualTo(2)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(2)->getOffset())->isEqualTo(1)
		;

		# String with end of line and just tags with attributes
		$tag1 = uniqid();
		$id1 = uniqid();
		$tag2 = uniqid();
		$id2 = uniqid();
		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag1 . ' id="' . $id1 . '" html="true" />';
		$secondTag = '<' . template\parser::defaultNamespace . ':' . $tag2 . ' id="' . $id2 . '" html="true" />';

		$ogoHtmlTemplateRoot = $parser->parseString($firstTag . template\parser::eol . $secondTag);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(3)
			->and
			->object($ogoHtmlTemplateRoot)->isInstanceOf('ogoHtmlTemplateRoot')
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag1)
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getId())->isEqualTo($id1)
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsEnabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(1))->isInstanceOf('ogoHtmlTemplateData')
			->and
			->string($ogoHtmlTemplateRoot->getChild(1)->getData())->isEqualTo(template\parser::eol)
			->and
			->object($ogoHtmlTemplateRoot->getChild(2))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(2)->getTag())->isEqualTo($tag2)
			->and
			->string($ogoHtmlTemplateRoot->getChild(2)->getId())->isEqualTo($id2)
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(2)->htmlIsEnabled())->isTrue()
			->and
			->string($ogoHtmlTemplateRoot->getChild(2)->getData())->isEmpty()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(2)->getLine())->isEqualTo(2)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(2)->getOffset())->isEqualTo(1)
		;

		# String with tag in a tag without attributes
		$tag1 = uniqid();
		$tag2 = uniqid();
		$firstOpenTag = '<' . template\parser::defaultNamespace . ':' . $tag1 . '>';
		$secondTag = '<' . template\parser::defaultNamespace . ':' . $tag2 . '/>';
		$firstCloseTag = '</' . template\parser::defaultNamespace . ':' . $tag1 . '>';

		$ogoHtmlTemplateRoot = $parser->parseString($firstOpenTag . $secondTag . $firstCloseTag);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag1)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(0)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsDisabled())->isTrue()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0)->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->getTag())->isEqualTo($tag2)
			->and
			->variable($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->getId())->isNull()
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->htmlIsDisabled())->isTrue()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->getOffset())->isEqualTo(strlen($firstOpenTag) + 1)
		;

		# String with tag in a tag with attributes
		$tag1 = uniqid();
		$id1 = uniqid();
		$tag2 = uniqid();
		$id2 = uniqid();
		$firstOpenTag = '<' . template\parser::defaultNamespace . ':' . $tag1 . ' id="' . $id1 . '" html="true">';
		$secondTag = '<' . template\parser::defaultNamespace . ':' . $tag2 . ' id="' . $id2 . '" html="true" />';
		$firstCloseTag = '</' . template\parser::defaultNamespace . ':' . $tag1 . '>';

		$ogoHtmlTemplateRoot = $parser->parseString($firstOpenTag . $secondTag . $firstCloseTag);

		$this->assert
			->sizeOf($ogoHtmlTemplateRoot->getChildren())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getTag())->isEqualTo($tag1)
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getId())->isEqualTo($id1)
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->htmlIsEnabled())->isTrue()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getOffset())->isEqualTo(1)
			->and
			->object($ogoHtmlTemplateRoot->getChild(0)->getChild(0))->isInstanceOf('ogoHtmlTemplateTag')
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->getTag())->isEqualTo($tag2)
			->and
			->string($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->getId())->isEqualTo($id2)
			->and
			->boolean($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->htmlIsEnabled())->isTrue()
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->getLine())->isEqualTo(1)
			->and
			->integer($ogoHtmlTemplateRoot->getChild(0)->getChild(0)->getOffset())->isEqualTo(strlen($firstOpenTag) + 1)
		;
	}
	*/
}

?>
