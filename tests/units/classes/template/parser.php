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
		$this
			->if($parser = new template\parser())
			->then
				->string($parser->getNamespace())->isEqualTo(template\parser::defaultNamespace)
				->object($parser->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->if($parser = new template\parser($namespace = uniqid()))
			->then
				->string($namespace)->isEqualTo($parser->getNamespace())
				->object($parser->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->if($parser = new template\parser($namespace = rand(1, PHP_INT_MAX)))
			->then
				->string($parser->getNamespace())->isEqualTo((string) $namespace)
				->object($parser->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
			->if($parser = new template\parser($namespace = uniqid(), $adapter = new atoum\test\adapter()))
			->then
				->string($parser->getNamespace())->isEqualTo((string) $namespace)
				->object($parser->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testSetNamespace()
	{
		$this
			->if($parser = new template\parser())
			->then
				->object($parser->setNamespace($namespace = uniqid()))->isIdenticalTo($parser)
				->string($parser->getNamespace())->isEqualTo($namespace)
				->object($parser->setNamespace($namespace = rand(1, PHP_INT_MAX)))->isIdenticalTo($parser)
				->string($parser->getNamespace())->isEqualTo((string) $namespace)
		;
	}

	public function testSetAdapter()
	{
		$this
			->if($parser = new template\parser())
			->then
				->object($parser->setAdapter($adapter = new atoum\test\adapter()))->isIdenticalTo($parser)
				->object($parser->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testCheckString()
	{
		$this->define->parserException = '\mageekguy\atoum\tests\units\asserters\template\parser\exception';

		$this
			->if($parser = new template\parser())
			->then
				->object($parser->checkString(uniqid()))->isIdenticalTo($parser)
				->object($parser->checkString('<' . uniqid() . ':' . uniqid() . ' />'))->isIdenticalTo($parser)
			->if($tag = uniqid())
			->then
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
				->object($parser->checkString('<' . template\parser::defaultNamespace . ':' . $tag . '></' . template\parser::defaultNamespace . ':' . $tag . '  ' . "\t" . '	>'))->isIdenticalTo($parser)
			->if($tagWithId = '<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . ($id = uniqid()) . '" />')
			->then
				->parserException(function() use ($parser, $tagWithId) {
						$parser->checkString($tagWithId . $tagWithId);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Id \'' . $id . '\' is already defined in line 1 at offset 1')
					->hasErrorLine(1)
					->hasErrorOffset(41)
			->if($tagWithInvalidAttribute = '<' . template\parser::defaultNamespace . ':' . $tag . ' foo="bar" />')
			->then
				->parserException(function() use ($parser, $tagWithInvalidAttribute) {
						$parser->checkString($tagWithInvalidAttribute);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Attribute \'foo\' is unknown')
					->hasErrorLine(1)
					->hasErrorOffset(1)
			->if($firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>')
			->then
				->parserException(function() use ($parser, $firstTag) {
						$parser->checkString($firstTag);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Tag \'' . $tag . '\' must be closed')
					->hasErrorLine(1)
					->hasErrorOffset(strlen($firstTag) + 1)
			->if($firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>')
			->and($eols = str_repeat("\n", rand(1, 10)))
			->then
				->parserException(function() use ($parser, $firstTag, $eols) {
						$parser->checkString($eols . $firstTag);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Tag \'' . $tag . '\' must be closed')
					->hasErrorLine(strlen($eols) + 1)
					->hasErrorOffset(strlen($firstTag) + 1)
			->if($spaces = str_repeat(' ', rand(1, 10)))
			->then
				->parserException(function() use ($parser, $firstTag, $eols, $spaces) {
						$parser->checkString($eols . $spaces . $firstTag);
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Tag \'' . $tag . '\' must be closed')
					->hasErrorLine(strlen($eols) + 1)
					->hasErrorOffset(strlen($firstTag) + strlen($spaces) + 1)
			->if($firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>')
			->and($secondTag = '</' . template\parser::defaultNamespace . ':' . ($notOpenTag = uniqid()) . '  ' . "\t" . '	>')
			->then
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

		$this
			->if($parser = new template\parser(null, $adapter = new test\adapter()))
			->and($adapter->file_get_contents = false)
			->then
				->exception(function() use ($parser, & $path) {
						$parser->checkFile($path = uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to get contents from file \'' . $path . '\'')
			->if($adapter->file_get_contents = '<' . uniqid() . ':' . uniqid() . ' />')
			->then
				->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
			->if($adapter->file_get_contents = '<')
			->then
				->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace)
			->then
				->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':')
			->then
				->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()))
			->then
				->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>')
			->then
				->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="" />')
			->then
				->parserException(function() use ($parser, $tag) {
						$parser->checkFile(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Id must not be empty')
					->hasErrorLine(1)
					->hasErrorOffset(1)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . uniqid() . '" />')
			->then
				->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . $tag . '></' . template\parser::defaultNamespace . ':' . $tag . '>')
			->then
				->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . $tag . '   ' . "\t" . '   ></' . template\parser::defaultNamespace . ':' . $tag . '>')
			->then
				->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . $tag . '></' . template\parser::defaultNamespace . ':' . $tag . '  ' . "\t" . '	>')
			->then
				->object($parser->checkFile(uniqid()))->isIdenticalTo($parser)
			->if($tagWithId = '<' . template\parser::defaultNamespace . ':' . $tag . ' id="' . ($id = uniqid()) . '" />')
			->and($adapter->file_get_contents = $tagWithId . $tagWithId)
			->then
				->parserException(function() use ($parser, $tagWithId) {
						$parser->checkFile(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Id \'' . $id . '\' is already defined in line 1 at offset 1')
					->hasErrorLine(1)
					->hasErrorOffset(41)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . $tag . ' foo="bar" />')
			->then
				->parserException(function() use ($parser) {
						$parser->checkFile(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Attribute \'foo\' is unknown')
					->hasErrorLine(1)
					->hasErrorOffset(1)
			->if($adapter->file_get_contents = $firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>')
			->then
				->parserException(function() use ($parser) {
						$parser->checkFile(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Tag \'' . $tag . '\' must be closed')
					->hasErrorLine(1)
					->hasErrorOffset(strlen($firstTag) + 1)
			->if($adapter->file_get_contents = ($eols = str_repeat("\n", rand(1, 10))) . ($firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>'))
			->then
				->parserException(function() use ($parser) {
						$parser->checkFile(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Tag \'' . $tag . '\' must be closed')
					->hasErrorLine(strlen($eols) + 1)
					->hasErrorOffset(strlen($firstTag) + 1)
			->if($adapter->file_get_contents = ($eols = str_repeat("\n", rand(1, 10))) . ($spaces = str_repeat(' ', rand(1, 10))) . ($firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>'))
			->then
				->parserException(function() use ($parser) {
						$parser->checkFile(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Tag \'' . $tag . '\' must be closed')
					->hasErrorLine(strlen($eols) + 1)
					->hasErrorOffset(strlen($firstTag) + strlen($spaces) + 1)
			->if($firstTag = '<' . template\parser::defaultNamespace . ':' . $tag . '>')
			->and($secondTag = '</' . template\parser::defaultNamespace . ':' . ($notOpenTag = uniqid()) . '  ' . "\t" . '	>')
			->and($adapter->file_get_contents = $firstTag . $secondTag)
			->then
				->parserException(function() use ($parser) {
						$parser->checkFile(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Tag \'' . $notOpenTag . '\' is not open')
					->hasErrorLine(1)
					->hasErrorOffset(strlen($firstTag) + 1)
			->if($adapter->file_get_contents = $eols . $firstTag . $secondTag)
			->then
				->parserException(function() use ($parser) {
						$parser->checkFile(uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->isInstanceOf('mageekguy\atoum\template\parser\exception')
					->hasMessage('Tag \'' . $notOpenTag . '\' is not open')
					->hasErrorLine(strlen($eols) + 1)
					->hasErrorOffset(strlen($firstTag) + 1)
			->if($adapter->file_get_contents = $eols . $spaces . $firstTag . $secondTag)
			->then
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
		$this
			->if($parser = new template\parser())
			->then
				->object($root = $parser->parseString(''))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->isEmpty()

				->object($root = $parser->parseString($string = uniqid()))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
				->string($root->getChild(0)->getData())->isEqualTo($string)

				->object($root = $parser->parseString($string = uniqid() . "\n" . uniqid() . "\n"))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
				->string($root->getChild(0)->getData())->isEqualTo($string)

				->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' />'))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
				->string($root->getChild(0)->getTag())->isEqualTo($tag)
				->variable($root->getChild(0)->getId())->isNull()
				->string($root->getChild(0)->getData())->isEmpty()
				->integer($root->getChild(0)->getLine())->isEqualTo(1)
				->integer($root->getChild(0)->getOffset())->isEqualTo(1)

				->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>'))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
				->string($root->getChild(0)->getTag())->isEqualTo($tag)
				->variable($root->getChild(0)->getId())->isNull()
				->string($root->getChild(0)->getData())->isEmpty()
				->integer($root->getChild(0)->getLine())->isEqualTo(1)
				->integer($root->getChild(0)->getOffset())->isEqualTo(1)

				->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '" />'))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
				->string($root->getChild(0)->getTag())->isEqualTo($tag)
				->string($root->getChild(0)->getId())->isEqualTo($id)
				->string($root->getChild(0)->getData())->isEmpty()
				->integer($root->getChild(0)->getLine())->isEqualTo(1)
				->integer($root->getChild(0)->getOffset())->isEqualTo(1)

				->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '"/>'))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
				->string($root->getChild(0)->getTag())->isEqualTo($tag)
				->string($root->getChild(0)->getId())->isEqualTo($id)
				->string($root->getChild(0)->getData())->isEmpty()
				->integer($root->getChild(0)->getLine())->isEqualTo(1)
				->integer($root->getChild(0)->getOffset())->isEqualTo(1)

				->object($root = $parser->parseString('<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '>' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>'))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
				->string($root->getChild(0)->getTag())->isEqualTo($tag)
				->variable($root->getChild(0)->getId())->isNull()
				->string($root->getChild(0)->getData())->isEmpty()
				->integer($root->getChild(0)->getLine())->isEqualTo(1)
				->integer($root->getChild(0)->getOffset())->isEqualTo(1)

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
		$this
			->if($parser = new template\parser(null, $adapter = new test\adapter()))
			->and($adapter->file_get_contents = false)
			->then
				->exception(function() use ($parser, & $path) {
						$parser->parseFile($path = uniqid());
					}
				)
					->isInstanceOf('mageekguy\atoum\exceptions\runtime')
					->hasMessage('Unable to get contents from file \'' . $path . '\'')
			->if($adapter->file_get_contents = '')
			->then
				->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->isEmpty()
			->if($adapter->file_get_contents = $string = uniqid())
			->then
				->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
				->string($root->getChild(0)->getData())->isEqualTo($string)
			->if($adapter->file_get_contents = $string = uniqid() . "\n" . uniqid() . "\n")
			->then
				->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\data')
				->string($root->getChild(0)->getData())->isEqualTo($string)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' />')
			->then
				->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
				->string($root->getChild(0)->getTag())->isEqualTo($tag)
				->variable($root->getChild(0)->getId())->isNull()
				->string($root->getChild(0)->getData())->isEmpty()
				->integer($root->getChild(0)->getLine())->isEqualTo(1)
				->integer($root->getChild(0)->getOffset())->isEqualTo(1)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>')
			->then
				->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
				->string($root->getChild(0)->getTag())->isEqualTo($tag)
				->variable($root->getChild(0)->getId())->isNull()
				->string($root->getChild(0)->getData())->isEmpty()
				->integer($root->getChild(0)->getLine())->isEqualTo(1)
				->integer($root->getChild(0)->getOffset())->isEqualTo(1)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '" />')
			->then
				->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
				->string($root->getChild(0)->getTag())->isEqualTo($tag)
				->string($root->getChild(0)->getId())->isEqualTo($id)
				->string($root->getChild(0)->getData())->isEmpty()
				->integer($root->getChild(0)->getLine())->isEqualTo(1)
				->integer($root->getChild(0)->getOffset())->isEqualTo(1)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '"/>')
			->then
				->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
				->string($root->getChild(0)->getTag())->isEqualTo($tag)
				->string($root->getChild(0)->getId())->isEqualTo($id)
				->string($root->getChild(0)->getData())->isEmpty()
				->integer($root->getChild(0)->getLine())->isEqualTo(1)
				->integer($root->getChild(0)->getOffset())->isEqualTo(1)
			->if($adapter->file_get_contents = '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '>' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>')
			->then
				->object($root = $parser->parseFile(uniqid()))->isInstanceOf('mageekguy\atoum\template')
				->array($root->getChildren())->hasSize(1)
				->object($root->getChild(0))->isInstanceOf('mageekguy\atoum\template\tag')
				->string($root->getChild(0)->getTag())->isEqualTo($tag)
				->variable($root->getChild(0)->getId())->isNull()
				->string($root->getChild(0)->getData())->isEmpty()
				->integer($root->getChild(0)->getLine())->isEqualTo(1)
				->integer($root->getChild(0)->getOffset())->isEqualTo(1)
			->if($adapter->file_get_contents = ($string1 = uniqid()) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '>' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>' . ($string2 = uniqid()))
			->then
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
			->if($adapter->file_get_contents = ($string1 = uniqid()) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . ' id="' . ($id = uniqid()) . '">' . ($data = uniqid()) . '</' . template\parser::defaultNamespace . ':' . $tag . '>' . ($string2 = uniqid()))
			->then
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
			->if($adapter->file_get_contents = ($string = str_repeat("\n", 6)) . '<' . template\parser::defaultNamespace . ':' . ($tag = uniqid()) . '/>')
			->then
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
