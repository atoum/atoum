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

	public function testExtract()
	{
		$this->assert
			->if($extractor = new annotations\extractor())
			->and($annotation = uniqid())
			->and($value = uniqid())
			->then
				->array($extractor->getAnnotations())->isEmpty()
				->object($extractor->extract(''))->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEmpty()
				->object($extractor->extract('#'))->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEmpty()
				->object($extractor->extract('//'))->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEmpty()
				->object($extractor->extract('/**/'))->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEmpty()
				->object($extractor->extract('/***/'))->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEmpty()
				->object($extractor->extract('/**' . self::space() . '*/'))->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEmpty()
				->object($extractor->extract(
						'/*' .
						self::space() .
						'@' .
						$annotation .
						self::space() .
						$value .
						self::space() .
						'*/'
					))->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEmpty()
				->object($extractor->extract(
						self::space() .
						'/' .
						self::star() .
						'@' .
						$annotation .
						self::space() .
						$value .
						self::space() .
						self::star(10, 1) .
						'/' .
						self::space()
					))->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEqualTo(array(
						$annotation => $value
					)
				)
				->object($extractor->extract(
						self::space() .
						'/' .
						self::star() .
						'@' .
						$annotation .
						self::space() .
						$value .
						($firstSpace = self::space()) .
						($otherValue = uniqid()) .
						($secondSpace = self::space()) .
						($anotherValue = uniqid()) .
						self::space() .
						self::star(10, 1) .
						'/' .
						self::space()
					))->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEqualTo(array(
						$annotation => $value . ' ' . $otherValue . ' ' . $anotherValue
					)
				)
		;
	}

	public function testResetAnnotations()
	{
		$this->assert
			->if($extractor = new annotations\extractor())
			->then
				->array($extractor->getAnnotations())->isEmpty()
				->object($extractor->resetAnnotations())->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEmpty()
			->if($extractor->extract('/** @foo bar */'))
			->then
				->array($extractor->getAnnotations())->isNotEmpty()
				->object($extractor->resetAnnotations())->isIdenticalTo($extractor)
				->array($extractor->getAnnotations())->isEmpty()
		;
	}

	public function testGetIterator()
	{
		$this->assert
			->if($extractor = new annotations\extractor())
			->object($extractor->getIterator())
				->isInstanceOf('arrayIterator')
				->isEmpty()
			->if($extractor = new annotations\extractor())
			->and($annotation = uniqid())
			->and($value = uniqid())
			->and($extractor->extract(
					self::space() .
					'/' .
					self::star() .
					'@' .
					$annotation .
					self::space() .
					$value .
					self::space() .
					self::star(10, 1) .
					'/' .
					self::space()
				)
			)
			->then
				->object($extractor->getIterator())
					->isInstanceOf('arrayIterator')
					->hasSize(1)
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

?>
