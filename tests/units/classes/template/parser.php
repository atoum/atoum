<?php

namespace mageekguy\atoum\tests\units\template;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\template
;

require_once __DIR__ . '/../../runner.php';

class parser extends atoum\test
{
	public function test__construct()
	{
		$parser = new template\parser();

		$this->assert
			->string($parser->getNamespace())->isEqualTo(template\parser::defaultNamespace)
			->object($parser->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
		;

		$parser = new template\parser($namespace = uniqid());

		$this->assert
			->string($namespace)->isEqualTo($parser->getNamespace())
			->object($parser->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
		;

		$parser = new template\parser($namespace = rand(1, PHP_INT_MAX));

		$this->assert
			->string($parser->getNamespace())->isEqualTo((string) $namespace)
			->object($parser->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
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
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
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
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
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
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
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
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $tag . '\' must be closed')
				->hasErrorLine(1)
				->hasErrorOffset(strlen($firstTag) + 1)
		;

		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>';

		$eols = str_repeat("\n", rand(1, 10));

		$this->assert
			->parserException(function() use ($parser, $firstTag, $eols) {
					$parser->checkString($eols . $firstTag);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $tag . '\' must be closed')
				->hasErrorLine(strlen($eols) + 1)
				->hasErrorOffset(strlen($firstTag) + 1)
		;

		$spaces = str_repeat(' ', rand(1, 10));

		$this->assert
			->parserException(function() use ($parser, $firstTag, $eols, $spaces) {
					$parser->checkString($eols . $spaces . $firstTag);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $tag . '\' must be closed')
				->hasErrorLine(strlen($eols) + 1)
				->hasErrorOffset(strlen($firstTag) + strlen($spaces) + 1)
		;

		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>';
		$secondTag = '</' . template\parser::defaultNamespace . ':' . ($notOpenTag = uniqid()) . '  ' . "\t" . '    >';

		$this->assert
			->parserException(function() use ($parser, $firstTag, $secondTag) {
					$parser->checkString($firstTag . $secondTag);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $notOpenTag . '\' is not open')
				->hasErrorLine(1)
				->hasErrorOffset(strlen($firstTag) + 1)
			->parserException(function() use ($parser, $firstTag, $secondTag, $eols) {
					$parser->checkString($eols . $firstTag . $secondTag);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $notOpenTag . '\' is not open')
				->hasErrorLine(strlen($eols) + 1)
				->hasErrorOffset(strlen($firstTag) + 1)
			->parserException(function() use ($parser, $firstTag, $secondTag, $eols, $spaces) {
					$parser->checkString($eols . $spaces . $firstTag . $secondTag);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $notOpenTag . '\' is not open')
				->hasErrorLine(strlen($eols) + 1)
				->hasErrorOffset(strlen($firstTag) + strlen($spaces) + 1)
		;
	}

	public function testCheckFile()
	{
		$this->define->parserException = '\mageekguy\atoum\tests\units\asserters\template\parser\exception';

		$parser = new template\parser(null, $adapter = new test\adapter());

		$adapter->file_get_contents = false;

		$this->assert
			->exception(function() use ($parser, & $path) {
					$parser->checkFile($path = uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to get contents from file \'' . $path . '\'')
		;

		$adapter->file_get_contents = '<' . uniqid() . ':' . uniqid() . ' />';

		$this->assert
			->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
		;

		$adapter->file_get_contents = '<';

		$this->assert
			->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace;

		$this->assert
			->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':';

		$this->assert
			->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid());

		$this->assert
			->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>';

		$this->assert
			->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="" />';

		$this->assert
			->parserException(function() use ($parser, $tag) {
					$parser->checkFile(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Id must not be empty')
				->hasErrorLine(1)
				->hasErrorOffset(1)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . uniqid() . '" />';

		$this->assert
			->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . $tag . '></' . template\parser::defaultNamespace . ':' . $tag . '>';

		$this->assert
			->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . $tag . '   ' . "\t" . '   ></' . template\parser::defaultNamespace . ':' . $tag . '>';

		$this->assert
			->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . $tag . '></' . template\parser::defaultNamespace . ':' . $tag . '  ' . "\t" . '    >';

		$this->assert
			->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
		;

		$tagWithId = '<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . ($id = uniqid()) . '" />';

		$adapter->file_get_contents = $tagWithId . $tagWithId;

		$this->assert
			->parserException(function() use ($parser, $tagWithId) {
					$parser->checkFile(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Id \'' . $id . '\' is already defined in line 1 at offset 1')
				->hasErrorLine(1)
				->hasErrorOffset(41)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . $tag . ' foo="bar" />';

		$this->assert
			->parserException(function() use ($parser) {
					$parser->checkFile(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Attribute \'foo\' is unknown')
				->hasErrorLine(1)
				->hasErrorOffset(1)
		;

		$adapter->file_get_contents = $firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>';

		$this->assert
			->parserException(function() use ($parser) {
					$parser->checkFile(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $tag . '\' must be closed')
				->hasErrorLine(1)
				->hasErrorOffset(strlen($firstTag) + 1)
		;

		$adapter->file_get_contents = ($eols = str_repeat("\n", rand(1, 10))) . ($firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>');

		$this->assert
			->parserException(function() use ($parser) {
					$parser->checkFile(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $tag . '\' must be closed')
				->hasErrorLine(strlen($eols) + 1)
				->hasErrorOffset(strlen($firstTag) + 1)
		;

		$adapter->file_get_contents = ($eols = str_repeat("\n", rand(1, 10))) . ($spaces = str_repeat(' ', rand(1, 10))) . ($firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>');

		$this->assert
			->parserException(function() use ($parser) {
					$parser->checkFile(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $tag . '\' must be closed')
				->hasErrorLine(strlen($eols) + 1)
				->hasErrorOffset(strlen($firstTag) + strlen($spaces) + 1)
		;

		$firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>';
		$secondTag = '</' . template\parser::defaultNamespace . ':' . ($notOpenTag = uniqid()) . '  ' . "\t" . '    >';

		$adapter->file_get_contents = $firstTag . $secondTag;

		$this->assert
			->parserException(function() use ($parser) {
					$parser->checkFile(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $notOpenTag . '\' is not open')
				->hasErrorLine(1)
				->hasErrorOffset(strlen($firstTag) + 1)
		;

		$adapter->file_get_contents = $eols . $firstTag . $secondTag;

		$this->assert
			->parserException(function() use ($parser) {
					$parser->checkFile(uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $notOpenTag . '\' is not open')
				->hasErrorLine(strlen($eols) + 1)
				->hasErrorOffset(strlen($firstTag) + 1)
		;

		$adapter->file_get_contents = $eols . $spaces . $firstTag . $secondTag;

		$this->assert
			->parserException(function() use ($parser, $firstTag, $secondTag, $eols, $spaces) {
					$parser->checkFile($eols . $spaces . $firstTag . $secondTag);
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->isInstanceOf('mageekguy\atoum\template\parser\exception')
				->hasMessage('Tag \'' . $notOpenTag . '\' is not open')
				->hasErrorLine(strlen($eols) + 1)
				->hasErrorOffset(strlen($firstTag) + strlen($spaces) + 1)
		;
	}

	public function testParseString()
	{
		$parser = new template\parser();

		$this->assert
			->object($root = $parser->parseString(''))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->isEmpty()
		;

		$this->assert
			->object($root = $parser->parseString($string = uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string)
		;

		$this->assert
			->object($root = $parser->parseString($string = uniqid() . "\n" . uniqid() . "\n"))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string)
		;

		$this->assert
			->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' />'))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->variable($root->getChild(0)->getId())->isNull()
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>'))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->variable($root->getChild(0)->getId())->isNull()
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '" />'))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->string($root->getChild(0)->getId())->isEqualTo($id)
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '"/>'))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->string($root->getChild(0)->getId())->isEqualTo($id)
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '>' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>'))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->variable($root->getChild(0)->getId())->isNull()
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$this->assert
			->object($root = $parser->parseString(($string1 = uniqid()) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '>' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>' . ($string2 = uniqid())))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(3)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string1)
			->object($root->getChild(1))->isInstanceOf('mageekguy\atoum\template\tag')
			->variable($root->getChild(1)->getId())->isNull()
			->string($root->getChild(1)->getData())->isEmpty()
			->integer($root->getChild(1)->getLine())->isEqualTo(1)
			->integer($root->getChild(1)->getOffset())->isEqualTo(strlen($string1) + 1)
			->object($root->getChild(2))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(2)->getData())->isEqualTo($string2)
		;

		$this->assert
			->object($root = $parser->parseString(($string1 = uniqid()) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '">' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>' . ($string2 = uniqid())))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(3)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string1)
			->object($root->getChild(1))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(1)->getId())->isEqualTo($id)
			->string($root->getChild(1)->getData())->isEmpty()
			->integer($root->getChild(1)->getLine())->isEqualTo(1)
			->integer($root->getChild(1)->getOffset())->isEqualTo(strlen($string1) + 1)
			->object($root->getChild(2))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(2)->getData())->isEqualTo($string2)
		;

		$this->assert
			->object($root = $parser->parseString(($string = str_repeat("\n", 6)) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>'))
			->array($root->getChildren())->hasSize(2)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string)
			->object($root->getChild(1))->isInstanceOf('mageekguy\atoum\template\tag')
			->variable($root->getChild(1)->getId())->isNull()
			->string($root->getChild(1)->getData())->isEmpty()
			->integer($root->getChild(1)->getLine())->isEqualTo(7)
			->integer($root->getChild(1)->getOffset())->isEqualTo(1)
		;
	}

	public function testParseFile()
	{
		$parser = new template\parser(null, $adapter = new test\adapter());

		$adapter->file_get_contents = false;

		$this->assert
			->exception(function() use ($parser, & $path) {
					$parser->parseFile($path = uniqid());
				}
			)
				->isInstanceOf('mageekguy\atoum\exceptions\runtime')
				->hasMessage('Unable to get contents from file \'' . $path . '\'')
		;

		$adapter->file_get_contents = '';

		$this->assert
			->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->isEmpty()
		;

		$adapter->file_get_contents = $string = uniqid();

		$this->assert
			->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string)
		;

		$adapter->file_get_contents = $string = uniqid() . "\n" . uniqid() . "\n";

		$this->assert
			->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' />';

		$this->assert
			->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->variable($root->getChild(0)->getId())->isNull()
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>';

		$this->assert
			->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->variable($root->getChild(0)->getId())->isNull()
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '" />';

		$this->assert
			->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->string($root->getChild(0)->getId())->isEqualTo($id)
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '"/>';

		$this->assert
			->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->string($root->getChild(0)->getId())->isEqualTo($id)
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '>' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>';

		$this->assert
			->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(1)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(0)->getTag())->isEqualTo($tag)
			->variable($root->getChild(0)->getId())->isNull()
			->string($root->getChild(0)->getData())->isEmpty()
			->integer($root->getChild(0)->getLine())->isEqualTo(1)
			->integer($root->getChild(0)->getOffset())->isEqualTo(1)
		;

		$adapter->file_get_contents = ($string1 = uniqid()) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '>' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>' . ($string2 = uniqid());

		$this->assert
			->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(3)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string1)
			->object($root->getChild(1))->isInstanceOf('mageekguy\atoum\template\tag')
			->variable($root->getChild(1)->getId())->isNull()
			->string($root->getChild(1)->getData())->isEmpty()
			->integer($root->getChild(1)->getLine())->isEqualTo(1)
			->integer($root->getChild(1)->getOffset())->isEqualTo(strlen($string1) + 1)
			->object($root->getChild(2))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(2)->getData())->isEqualTo($string2)
		;

		$adapter->file_get_contents = ($string1 = uniqid()) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '">' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>' . ($string2 = uniqid());

		$this->assert
			->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
			->array($root->getChildren())->hasSize(3)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string1)
			->object($root->getChild(1))->isInstanceOf('mageekguy\atoum\template\tag')
			->string($root->getChild(1)->getId())->isEqualTo($id)
			->string($root->getChild(1)->getData())->isEmpty()
			->integer($root->getChild(1)->getLine())->isEqualTo(1)
			->integer($root->getChild(1)->getOffset())->isEqualTo(strlen($string1) + 1)
			->object($root->getChild(2))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(2)->getData())->isEqualTo($string2)
		;

		$adapter->file_get_contents = ($string = str_repeat("\n", 6)) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>';

		$this->assert
			->object($root = $parser->parseFile(uniqid()))
			->array($root->getChildren())->hasSize(2)
			->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
			->string($root->getChild(0)->getData())->isEqualTo($string)
			->object($root->getChild(1))->isInstanceOf('mageekguy\atoum\template\tag')
			->variable($root->getChild(1)->getId())->isNull()
			->string($root->getChild(1)->getData())->isEmpty()
			->integer($root->getChild(1)->getLine())->isEqualTo(7)
			->integer($root->getChild(1)->getOffset())->isEqualTo(1)
		;
	}
}
