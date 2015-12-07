<?php

namespace mageekguy\atoum\test\phpunit\mock;

use mageekguy\atoum;

class generator extends atoum\test\mock\generator
{
	protected function generateClassCode(\reflectionClass $class, $mockNamespace, $mockClass)
	{
		$code = 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
			'final class ' . $mockClass . ' extends \\' . $class->getName() . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
			'{' . PHP_EOL .
			static::generateMockControllerMethods() .
			$this->generateClassMethodCode($class) .
			'}' . PHP_EOL .
			'}'
		;

		return $code;
	}

	protected static function generateMockControllerMethods()
	{
		return parent::generateMockControllerMethods() .
			"\t" . 'protected $phpUnitMockdefinition;' . PHP_EOL .
			"\t" . 'public function getMockDefinition()' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'if (null === $this->phpUnitMockdefinition)' . PHP_EOL .
			"\t\t" . '{' . PHP_EOL .
			"\t\t\t" . '$this->phpUnitMockdefinition = new \\' . __NAMESPACE__ . '\\definition($this);' . PHP_EOL .
			"\t\t" . '}' . PHP_EOL .
			"\t\t" . 'return $this->phpUnitMockdefinition;' . PHP_EOL .
			"\t" . '}' . PHP_EOL .
			"\t" . 'public function expects($expectation)' . PHP_EOL .
			"\t" . '{' . PHP_EOL .
			"\t\t" . 'return $this->getMockDefinition()->expects($expectation);' . PHP_EOL .
			"\t" . '}' . PHP_EOL
		;
	}

	protected static function generateUnknownClassCode($class, $mockNamespace, $mockClass)
	{
		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
			'final class ' . $mockClass . ' implements \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
			'{' . PHP_EOL .
			static::generateMockControllerMethods() .
			self::generateDefaultConstructor(true) .
			self::generate__call() .
			self::generateGetMockedMethod(array('__call')) .
			'}' . PHP_EOL .
			'}'
		;
	}

	protected function generateInterfaceCode(\reflectionClass $class, $mockNamespace, $mockClass)
	{
		$addIteratorAggregate = (
			$class->isInstantiable() === false
			&& (
				$class->implementsInterface('traversable') === true
				&& $class->implementsInterface('iterator') === false
				&& $class->implementsInterface('iteratorAggregate') === false
			)
		);

		return 'namespace ' . ltrim($mockNamespace, '\\') . ' {' . PHP_EOL .
			'final class ' . $mockClass . ' implements \\' . ($addIteratorAggregate === false ? '' : 'iteratorAggregate, \\') . $class->getName() . ', \\' . __NAMESPACE__ . '\\aggregator' . PHP_EOL .
			'{' . PHP_EOL .
			static::generateMockControllerMethods() .
			$this->generateInterfaceMethodCode($class, $addIteratorAggregate) .
			'}' . PHP_EOL .
			'}'
		;
	}
}
