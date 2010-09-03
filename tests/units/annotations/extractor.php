<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;
use \mageekguy\atoum\annotations;

require_once(__DIR__ . '/../../../runners/autorunner.php');

/**
@isolation off
*/
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
			->string('*****')->match('/\*{2,10}/')
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
			->object($extractor)->isInstanceOf('\iteratorAggregate')
			->collection($extractor->getAnnotations())->isEmpty()
		;

		$extractor = new annotations\extractor('');

		$this->assert
			->object($extractor)->isInstanceOf('\iteratorAggregate')
			->collection($extractor->getAnnotations())->isEmpty()
		;

		$extractor = new annotations\extractor('#');

		$this->assert
			->object($extractor)->isInstanceOf('\iteratorAggregate')
			->collection($extractor->getAnnotations())->isEmpty()
		;

		$extractor = new annotations\extractor('/* */');

		$this->assert
			->object($extractor)->isInstanceOf('\iteratorAggregate')
			->collection($extractor->getAnnotations())->isEmpty()
		;

		$extractor = new annotations\extractor('/** */');

		$this->assert
			->object($extractor)->isInstanceOf('\iteratorAggregate')
			->collection($extractor->getAnnotations())->isEmpty()
		;

		$annotation = uniqid();
		$value = uniqid();

		$extractor = new annotations\extractor('/* @' . $annotation . ' ' . $value . ' */');

		$this->assert
			->object($extractor)->isInstanceOf('\iteratorAggregate')
			->collection($extractor->getAnnotations())->isEmpty()
		;

		$annotation = uniqid();
		$value = uniqid();

		$extractor = new annotations\extractor('/' . self::star() . ' @' . $annotation . ' ' . $value . ' ' . self::star(10, 1) . '/');

		$this->assert
			->object($extractor)->isInstanceOf('\iteratorAggregate')
			->collection($extractor->getAnnotations())->isEqualTo(array(
					$annotation => $value
				)
			)
		;

		$annotation = uniqid();
		$value = uniqid();

		$extractor = new annotations\extractor(self::space() . '/' . self::star() . ' @' . $annotation . ' ' . $value . ' ' . self::star(10, 1) . '/');

		$this->assert
			->object($extractor)->isInstanceOf('\iteratorAggregate')
			->collection($extractor->getAnnotations())->isEqualTo(array(
					$annotation => $value
				)
			)
		;

		$annotation = uniqid();
		$value = uniqid();

		$extractor = new annotations\extractor('/' . self::star() . "\n" . self::space() . self::star() . self::space() . '@' . $annotation . ' ' . $value . ' ' . self::star(10, 1) . '/');

		$this->assert
			->object($extractor)->isInstanceOf('\iteratorAggregate')
			->collection($extractor->getAnnotations())->isEqualTo(array(
					$annotation => $value
				)
			)
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
