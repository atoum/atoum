<?php

namespace mageekguy\atoum\tests\units\annotations;

use
	mageekguy\atoum,
	mageekguy\atoum\annotations
;

require_once(__DIR__ . '/../../runner.php');

class extractor extends atoum\test
{
	public function testSpace()
	{
		$this->assert
			->string(self::space())->match('/ {1,10}/')
		;

		$this->assert
			->string(self::space(5))->match('/ {1,5}/')
		;

		$this->assert
			->string(self::space(5, 3))->match('/ {3,5}/')
		;
	}

	public function testStar()
	{
		$this->assert
			->string(self::star())->match('/\*{2,10}/')
		;

		$this->assert
			->string(self::star(5))->match('/\*{2,5}/')
		;

		$this->assert
			->string(self::star(5, 3))->match('/\*{3,5}/')
		;
	}

	public function test__contruct()
	{
		$extractor = new annotations\extractor();

		$this->assert
			->object($extractor)->isInstanceOf('iteratorAggregate')
			->array($extractor->getAnnotations())->isEmpty()
		;

		$extractor = new annotations\extractor('');

		$this->assert
			->object($extractor)->isInstanceOf('iteratorAggregate')
			->array($extractor->getAnnotations())->isEmpty()
		;

		$extractor = new annotations\extractor('#');

		$this->assert
			->object($extractor)->isInstanceOf('iteratorAggregate')
			->array($extractor->getAnnotations())->isEmpty()
		;

		$extractor = new annotations\extractor('//');

		$this->assert
			->object($extractor)->isInstanceOf('iteratorAggregate')
			->array($extractor->getAnnotations())->isEmpty()
		;

		$extractor = new annotations\extractor('/**/');

		$this->assert
			->object($extractor)->isInstanceOf('iteratorAggregate')
			->array($extractor->getAnnotations())->isEmpty()
		;

		$extractor = new annotations\extractor('/** */');

		$this->assert
			->object($extractor)->isInstanceOf('iteratorAggregate')
			->array($extractor->getAnnotations())->isEmpty()
		;

		$annotation = uniqid();
		$value = uniqid();

		$extractor = new annotations\extractor(
			'/*' .
			self::space() .
			'@' .
			$annotation .
			self::space() .
			$value .
			self::space() .
			'*/'
		);

		$this->assert
			->object($extractor)->isInstanceOf('iteratorAggregate')
			->array($extractor->getAnnotations())->isEmpty()
		;

		$annotation = uniqid();
		$value = uniqid();

		$extractor = new annotations\extractor(
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
		);

		$this->assert
			->object($extractor)->isInstanceOf('iteratorAggregate')
			->array($extractor->getAnnotations())->isEqualTo(array(
					$annotation => $value
				)
			)
		;
	}

	public function testExtract()
	{
		$annotation = uniqid();
		$value = uniqid();

		$extractor = new annotations\extractor();

		$this->assert
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
		;
	}

	public function testGetIterator()
	{
		$annotation = uniqid();
		$value = uniqid();

		$extractor = new annotations\extractor();

		$this->assert
			->object($extractor->getIterator())
				->isInstanceOf('arrayIterator')
				->isEmpty()
		;

		$extractor = new annotations\extractor(
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
		);

		$this->assert
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
