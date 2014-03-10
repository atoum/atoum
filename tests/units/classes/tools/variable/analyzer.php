<?php

namespace mageekguy\atoum\tests\units\tools\variable;

require_once __DIR__ . '/../../../runner.php';

use
	mageekguy\atoum
;

class analyzer extends atoum\test
{
	public function testGetType()
	{
		$this
			->given($this->newTestedInstance)
			->then

				->if($this->function->gettype = 'boolean')
				->then
					->string($this->testedInstance->getTypeOf(true))->isEqualTo('boolean(true)')
					->string($this->testedInstance->getTypeOf(false))->isEqualTo('boolean(false)')

				->if($this->function->gettype = 'integer')
				->then
					->string($this->testedInstance->getTypeOf($integer = uniqid()))->isEqualTo('integer(' . $integer . ')')

				->if($this->function->gettype = 'double')
				->then
					->string($this->testedInstance->getTypeOf($float = uniqid()))->isEqualTo('float(' . $float . ')')

				->if($this->function->gettype = 'NULL')
				->then
					->string($this->testedInstance->getTypeOf($float = uniqid()))->isEqualTo('null')

				->if(
					$this->function->gettype = 'object',
					$this->function->get_class = $class = uniqid()
				)
				->then
					->string($this->testedInstance->getTypeOf(uniqid()))->isEqualTo('object(' . $class . ')')

				->if(
					$this->function->gettype = 'resource',
					$this->function->get_resource_type = $type = uniqid()
				)
				->then
					->string($this->testedInstance->getTypeOf($resource = uniqid()))->isEqualTo($resource . ' of type ' . $type)

				->if($this->function->gettype = 'string')
				->then
					->string($this->testedInstance->getTypeOf($string = uniqid()))->isEqualTo('string(' . strlen($string) . ') \'' . $string . '\'')

				->if(
					$this->function->gettype = 'array',
					$this->function->sizeof = $size = rand(1, PHP_INT_MAX)
				)
				->then
					->string($this->testedInstance->getTypeOf(uniqid()))->isEqualTo('array(' . $size . ')')
		;
	}

	public function testDump()
	{
		$this
			->given($this->newTestedInstance)

			->if(
				$this->function->ob_start->doesNothing(),
				$this->function->var_dump->doesNothing(),
				$this->function->ob_get_clean = ('     ' . ($dump = uniqid()) . '        ' . PHP_EOL)
			)
			->then
				->string($this->testedInstance->dump($this))->isEqualTo($dump)
		;
	}

	public function testIsObject()
	{
		$this
			->given($this->newTestedInstance)

			->if($this->function->is_object = false)
			->then
				->boolean($this->testedInstance->isObject($mixed = uniqid()))->isFalse
				->function('is_object')->wasCalledWithArguments($mixed)->once

			->if($this->function->is_object = true)
			->then
				->boolean($this->testedInstance->isObject($mixed = uniqid()))->isTrue
				->function('is_object')->wasCalledWithArguments($mixed)->once
		;
	}

	public function testIsBoolean()
	{
		$this
			->given($this->newTestedInstance)

			->if($this->function->is_bool[1] = false)
			->then
				->boolean($this->testedInstance->isBoolean($mixed = uniqid()))->isFalse
				->function('is_bool')->wasCalledWithIdenticalArguments($mixed)->once

			->if($this->function->is_bool[3] = true)
			->then
				->boolean($this->testedInstance->isBoolean($mixed = uniqid()))->isTrue
				->function('is_bool')->wasCalledWithIdenticalArguments($mixed)->once
		;
	}

	public function testIsInteger()
	{
		$this
			->given($this->newTestedInstance)

			->if($this->function->is_int = false)
			->then
				->boolean($this->testedInstance->isInteger($mixed = uniqid()))->isFalse
				->function('is_int')->wasCalledWithArguments($mixed)->once

			->if($this->function->is_int = true)
			->then
				->boolean($this->testedInstance->isInteger($mixed = uniqid()))->isTrue
				->function('is_int')->wasCalledWithArguments($mixed)->once
		;
	}

	public function testIsFloat()
	{
		$this
			->given($this->newTestedInstance)

			->if($this->function->is_float = false)
			->then
				->boolean($this->testedInstance->isFloat($mixed = uniqid()))->isFalse
				->function('is_float')->wasCalledWithArguments($mixed)->once

			->if($this->function->is_float = true)
			->then
				->boolean($this->testedInstance->isFloat($mixed = uniqid()))->isTrue
				->function('is_float')->wasCalledWithArguments($mixed)->once
		;
	}

	public function testIsString()
	{
		$this
			->given($this->newTestedInstance)

			->if($this->function->is_string = false)
			->then
				->boolean($this->testedInstance->isString($mixed = uniqid()))->isFalse
				->function('is_string')->wasCalledWithArguments($mixed)->once

			->if($this->function->is_string = true)
			->then
				->boolean($this->testedInstance->isString($mixed = uniqid()))->isTrue
				->function('is_string')->wasCalledWithArguments($mixed)->once
		;
	}

	public function testIsArray()
	{
		$this
			->given($this->newTestedInstance)

			->if($this->function->is_array = false)
			->then
				->boolean($this->testedInstance->isArray($mixed = uniqid()))->isFalse
				->function('is_array')->wasCalledWithArguments($mixed)->once

			->if($this->function->is_array = true)
			->then
				->boolean($this->testedInstance->isArray($mixed = uniqid()))->isTrue
				->function('is_array')->wasCalledWithArguments($mixed)->once
		;
	}

	public function testIsUtf8()
	{
		$this
			->given($this->newTestedInstance)

			->if(
				$this->function->is_string = false,
				$this->function->preg_match = 0
			)
			->then
				->boolean($this->testedInstance->isUtf8($mixed = uniqid()))->isFalse
				->function('is_string')->wasCalledWithArguments($mixed)->once
				->function('preg_match')->wasCalledWithArguments('/^.*$/us', $mixed)->never

			->if($this->function->is_string = true)
			->then
				->boolean($this->testedInstance->isUtf8($mixed = uniqid()))->isFalse
				->function('is_string')->wasCalledWithArguments($mixed)->once
				->function('preg_match')->wasCalledWithArguments('/^.*$/us', $mixed)->once

			->if($this->function->preg_match = 1)
			->then
				->boolean($this->testedInstance->isUtf8($mixed = uniqid()))->isTrue
				->function('is_string')->wasCalledWithArguments($mixed)->once
				->function('preg_match')->wasCalledWithArguments('/^.*$/us', $mixed)->once
		;
	}

	private function getRandomUtf8String()
	{
		$characters = 'àâäéèêëîïôöùüŷÿ';
		$characters = mb_convert_encoding($characters, 'UTF-8', mb_detect_encoding($characters));
		$charactersLength = mb_strlen($characters, 'UTF-8');

		$utf8String = '';

		for($i = 0; $i < 16; $i++)
		{
			$utf8String .= mb_substr($characters, rand(0, $charactersLength - 1), 1, 'UTF-8');
		}

		return $utf8String;
	}
}
