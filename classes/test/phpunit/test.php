<?php

namespace mageekguy\atoum\test\phpunit;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\annotations,
	mageekguy\atoum\test\phpunit
	;

abstract class test extends atoum\test
{
	const defaultNamespace = '#^#';

	private $unsupportedMethods = array();
	private $mocks = array();

	public function setMockGenerator(atoum\test\mock\generator $generator = null)
	{
		return parent::setMockGenerator($generator ?: new phpunit\mock\generator($this));
	}

	public function setMockControllerLinker(atoum\mock\controller\linker $linker = null)
	{
		return parent::setMockControllerLinker($linker ?: new phpunit\mock\controller\linker($this));
	}

	public function addUnsupportedMethod($testMethod, $reason)
	{
		if (isset($this->unsupportedMethods[$testMethod]) === false) {
			$this->unsupportedMethods[$testMethod] = $reason;
		}

		return $this;
	}

	public function getUnsupportedMethods()
	{
		return $this->unsupportedMethods;
	}

	public function addMock(phpunit\mock\aggregator $mock)
	{
		$this->mocks[] = $mock;

		return $this;
	}

	public function getMocks()
	{
		return $this->mocks;
	}

	public function beforeTestMethod($testMethod)
	{
		if(isset($this->unsupportedMethods[$testMethod])) {
			$this->skip($this->unsupportedMethods[$testMethod]);
		}
	}

	protected function assertMocks()
	{
		foreach($this->mocks as $mock) {
			$mock->getMockDefinition()->verdict($this);
		}

		return $this;
	}

	protected function setMethodAnnotations(annotations\extractor $extractor, & $methodName)
	{
		parent::setMethodAnnotations($extractor, $methodName);

		$self = $this;
		$extractor
			->setHandler('group', function($value) use ($self, & $methodName) { $self->setMethodTags($methodName, annotations\extractor::toArray($value)); })
			->setHandler('expectedException', function($value) use ($self, & $methodName) {
				if($value) {
					$method = new \ReflectionMethod($self->getClass(), $methodName);
					$start = $method->getStartLine();
					$end = $method->getEndLine();
					$code = file($self->getPath());
					$inner = array_slice($code, $start, ($end - $start));
					array_walk(
						$inner,
						function(& $value, $key) {
							$value = str_replace("\t", '    ', $value);
							$value = $key === 0 ? trim($value) . PHP_EOL : '        ' . $value;
						}
					);

					preg_match('/\((.*?)\)/', $code[$start - 1], $matches);

					$message = '@expectedException is not supported.';
					$code = 'You should rewrite your test method as follow:' . PHP_EOL
						. '    // ' . $self->getPath() . ':' . $start . PHP_EOL
						. $code[$start - 1] . '    {' . PHP_EOL
						. '        if(method_exists($this, \'markTestIncomplete\')) {' . PHP_EOL
						. '            $this->markTestIncomplete(\'This is an atoum test\');' . PHP_EOL
						. '        }' . PHP_EOL . PHP_EOL
						. '        $this' . PHP_EOL
						. '            ->exception(function() ' . (isset($matches[1]) && $matches[1] != '' ? 'use (' . $matches[1] . ') ' : '') . trim(join('', $inner)) . ')' . PHP_EOL
						. '                ->isInstanceOf(\'\\\\' . trim($value, '\\') . '\')' . PHP_EOL
						. '                //->hasMessage(\'Exception message\')' . PHP_EOL
						. '        ;' . PHP_EOL
						. '    }' . PHP_EOL
					;

					$self->addUnsupportedMethod($methodName,  $message . $code);
				}
			})
		;

		return $this;
	}

	public function setAssertionManager(atoum\test\assertion\manager $assertionManager = null)
	{
		$assertionManager = parent::setAssertionManager($assertionManager)->getAssertionManager();
		$self = $this;

		$assertionManager
			->setHandler('assertFalse', function($value, $failMessage = null) use ($self) {
				return $self->boolean($value)->isFalse($failMessage);
			})
			->setHandler('assertTrue', function($value, $failMessage = null) use ($self) {
				return $self->boolean($value)->isTrue($failMessage);
			})
			->setHandler('assertNull', function($value, $failMessage = null) use ($self) {
				return $self->variable($value)->isNull($failMessage);
			})
			->setHandler('assertNotNull', function($value, $failMessage = null) use ($self) {
				return $self->variable($value)->isNotNull($failMessage);
			})
			->setHandler('assertEquals', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->variable($actual)->isEqualTo($expected, $failMessage);
			})
			->setHandler('assertNotEquals', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->variable($actual)->isNotEqualTo($expected, $failMessage);
			})
			->setHandler('assertContains', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string($actual)->contains($expected, $failMessage);
			})
			->setHandler('assertNotContains', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string($actual)->notContains($expected, $failMessage);
			})
			->setHandler('assertSame', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->variable($actual)->isIdenticalTo($expected, $failMessage);
			})
			->setHandler('assertInstanceof', $assertInstanceOf = function($expected, $actual, $failMessage = null) use ($self) {
				return $self->object($actual)->isInstanceOf($expected, $failMessage);
			})
			->setHandler('assertInstanceOf', $assertInstanceOf)
			->setHandler('assertNotInstanceof', function($expected, $actual, $failMessage = null) use ($self) {
				$assert = null;

				if(is_object($actual) === false) {
					return $self;
				}

				return $self->object($actual)->isNotInstanceOf($expected, $failMessage);
			})
			->setHandler('assertArrayHasKey', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->array($actual)->hasKey($expected, $failMessage);
			})
			->setHandler('assertCount', function($expected, $actual, $failMessage = null) use ($self) {
				switch (true)
				{
					case $actual instanceof \Countable:
						return $self->object($actual)->hasSize($expected, $failMessage);

					default:
						return $self->array($actual)->hasSize($expected, $failMessage);
				}
			})
			->setHandler('markTestSkipped', function($skipMessage) use ($self) {
				foreach($self->getMocks() as $mock) {
					$mock->getMockDefinition()->reset();
				}

				return $self->skip($skipMessage);
			})
			->setHandler('getMock', $getMockHandler = function($class, $methods = array(), $args = array(), $mockClassName = null, $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true, $cloneArguments = false) use ($self) {
				if($callOriginalConstructor === false) {
					$self->getMockGenerator()->orphanize('__construct');
					$self->getMockGenerator()->shuntParentClassCalls();
				}

				$classname = '\\' . $self->getMockGenerator()->getDefaultnamespace() . '\\' . trim($mockClassName ?: $class ,'\\');

				if (class_exists($classname) === false)
				{
					$self->getMockGenerator()->generate($class, $mockClassName);
				}

				$mock = null;
				if(sizeof($args) > 0) {
					$reflection = new \ReflectionClass($classname);
					$mock = $reflection->newInstanceArgs($args);
				}

				$mock = $mock ?: new $classname();
				$self->addMock($mock);

				foreach($methods as $method) {
					$mock->getMockController()->{$method} = null;
				}

				return $mock;
			})
			->setHandler('getMockForAbstractClass', function($class, $args = array(), $mockClassName = null, $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true) use ($self) {
				return $self->getMock($class, array(), $args, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload);
			})
			->setHandler('getMockBuilder', function($class) use ($self) {
				$mockBuilder = new mock\builder($self, $class);

				return $mockBuilder;
			})
			->setHandler('setExpectedException', function($class) use ($self) {
				foreach($self->getMocks() as $mock) {
					$mock->getMockDefinition()->reset();
				}

				return $self->skip('Testing exception is not available');
			})
			->setHandler('assertFileEquals', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string(file_get_contents($actual))->isEqualToContentsOffile($expected, $failMessage);
			})
			->setHandler('assertInternalType', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->{$expected}($actual, $failMessage);
			})
			->setHandler('assertEmpty', function($actual, $failMessage = null) use ($self) {
				switch (true)
				{
					case is_object($actual):
						return $self->object($actual)->isEmpty($failMessage);

					case is_array($actual):
						return $self->array($actual)->isEmpty($failMessage);

					case is_string($actual):
						return $self->string($actual)->isEmpty($failMessage);
				}

				return $self;
			})
			->setHandler('assertGreaterThanOrEqual', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->integer($actual)->isGreaterThanOrEqualTo($expected, $failMessage);
			})
			->setHandler('assertGreaterThan', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->integer($actual)->isGreaterThan($expected, $failMessage);
			})
			->setHandler('assertRegExp', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string($actual)->match($expected, $failMessage);
			})
			->setHandler('exactly', function($value) {
				return $value;
			})
			->setHandler('once', function() {
				return 1;
			})
			->setHandler('atLeastOnce', function() {
				return '>=1';
			})
			->setHandler('never', function() {
				return 0;
			})
			->setHandler('returnCallback', function($value) {
				return $value;
			})
			->setHandler('returnValue', function($value) {
				return $value;
			})
			->setHandler('any', function() {
				return null;
			})
			->setHandler('at', function($index) {
				return '@:' . ($index + 1);
			})
			->setHandler('equalTo', function($value) {
				return $value;
			})
			->setHandler('isInstanceOf', function($value) {
				return $value;
			})
			->setHandler('identicalTo', function($value) {
				return $value;
			})
			->setHandler('matchesRegularExpression', function() use ($self) {
				$self->skip('matchesRegularExpression is not supported');
			})
			->setHandler('stringContains', function() use ($self) {
				$self->skip('stringContains is not supported');
			})
			->setHandler('onConsecutiveCalls', function() {
				return new phpunit\call\consecutive(func_get_args());
			})
			->setHandler('throwException', function(\exception $exception) {
				return new phpunit\call\throwing($exception);
			})
			->setHandler('fail', function($failMessage = null) use ($self) {
				throw new atoum\asserter\exception($failMessage);
			})
			->setHandler('assertFileNotExists', function($expected, $failMessage = null) use ($self) {
				return $self->boolean(file_exists($expected))->isFalse(sprintf($failMessage ?: 'File %s exists', $expected));
			})
			->setHandler('assertFileExists', function($expected, $failMessage = null) use ($self) {
				return $self->boolean(file_exists($expected))->isTrue(sprintf($failMessage ?: 'File %s does not exist', $expected));
			})
			->setHandler('assertStringStartsWith', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string($actual)->match('/^' . preg_quote($expected, '/') . '/', $failMessage);
			})
			->setHandler('assertAttributeEquals', function($expected, $attribute, $object, $failMessage = null) use ($self) {
				$class = is_object($object) ? new \ReflectionObject($object) : new \ReflectionClass($object);
				$property = $class->getProperty($attribute);
				$property->setAccessible(true);
				$actual = $property->getValue($object);
				$property->setAccessible(false);

				return $self->variable($actual)->isEqualTo($expected, $failMessage);
			})
		;

		return $this;
	}
}
