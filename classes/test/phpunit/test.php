<?php

namespace mageekguy\atoum\test\phpunit;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\annotations,
	mageekguy\atoum\test\phpunit,
	mageekguy\atoum\adapter
;

abstract class test extends atoum\test
{
	const defaultNamespace = '#(?:^|\\\)Tests?\\\#i';
	const defaultTestedClass = '#Test$#';
	const defaultEngine = 'inline';

	private $unsupportedMethods = array();

	public function setAsserterGenerator(atoum\test\asserter\generator $generator = null)
	{
		parent::setAsserterGenerator($generator ?: new phpunit\asserter\generator($this));

		return $this;
	}

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

	public function beforeTestMethod($testMethod)
	{
		if(isset($this->unsupportedMethods[$testMethod])) {
			$this->skip($this->unsupportedMethods[$testMethod]);
		}
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
						. '    ' . trim($code[$start - 1]) . '    {' . PHP_EOL
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
			->setHandler('assertNull', function($value, $failMessage = null) use ($self) {
				return $self->variable($value)->isNull($failMessage);
			})
			->setHandler('assertNotNull', function($value, $failMessage = null) use ($self) {
				return $self->variable($value)->isNotNull($failMessage);
			})
			->setHandler('assertSame', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->variable($actual)->isIdenticalTo($expected, $failMessage);
			})
			->setHandler('assertNotSame', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->variable($actual)->isNotIdenticalTo($expected, $failMessage);
			})
			->setHandler('markTestSkipped', function($skipMessage) use ($self) {
				$self->getMockControllerLinker()->init();

				$self->skip($skipMessage);
			})
			->setHandler('getMockBuilder', function($class) use ($self) {
				$mockBuilder = new mock\builder($self, $class);

				return $mockBuilder;
			})
			->setHandler('getMock', $getMockHandler = function($class, $methods = array(), $args = array(), $mockClassName = null, $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true, $cloneArguments = false) use ($self) {
				return $builder = $this->getMockBuilder($class)
					->setMethods($methods)
					->setConstructorArgs($args)
					->setMockClassName($mockClassName)
					->enableOriginalConstructor($callOriginalConstructor)
					->enableOriginalClone($callOriginalClone)
					->enableAutoload($callAutoload)
					->enableArgumentCloning($cloneArguments)
					->getMock()
				;
			})
			->setHandler('getMockForAbstractClass', function($class, $args = array(), $mockClassName = null, $callOriginalConstructor = true, $callOriginalClone = true, $callAutoload = true) use ($self) {
				return $self->getMock($class, array(), $args, $mockClassName, $callOriginalConstructor, $callOriginalClone, $callAutoload);
			})
			->setHandler('assertFileEquals', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string(file_get_contents($actual))->isEqualToContentsOfFile($expected, $failMessage);
			})
			->setHandler('assertStringEqualsFile', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string($actual)->isEqualToContentsOfFile($expected, $failMessage);
			})
			->setHandler('assertInternalType', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->{$expected}($actual, $failMessage);
			})
			->setHandler('assertGreaterThanOrEqual', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->integer($actual)->isGreaterThanOrEqualTo($expected, $failMessage);
			})
			->setHandler('assertGreaterThan', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->integer($actual)->isGreaterThan($expected, $failMessage);
			})
			->setHandler('assertLessThanOrEqual', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->integer($actual)->isLessThanOrEqualTo($expected, $failMessage);
			})
			->setHandler('assertLessThan', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->integer($actual)->isLessThan($expected, $failMessage);
			})
			->setHandler('assertRegExp', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string($actual)->match($expected, $failMessage);
			})
			->setHandler('exactly', function($value) {
				return new phpunit\mock\definition\expectations\exactly($value);
			})
			->setHandler('once', function() use ($self) {
				return $self->exactly(1);
			})
			->setHandler('atLeastOnce', function() {
				return new phpunit\mock\definition\expectations\atLeastOnce();
			})
			->setHandler('never', function() {
				return new phpunit\mock\definition\expectations\never();
			})
			->setHandler('any', function() {
				return null;
			})
			->setHandler('at', function($index) {
				return $index + 1;
			})
			->setHandler('returnCallback', function($value) {
				return new phpunit\mock\definition\call\returning($value);
			})
			->setHandler('returnValue', function($value) {
				return new phpunit\mock\definition\call\returning($value);
			})
			->setHandler('equalTo', function($value) {
				return $value;
			})
			->setHandler('identicalTo', function($value) {
				return $value;
			})
			->setHandler('onConsecutiveCalls', function() {
				return new phpunit\mock\definition\call\consecutive(func_get_args());
			})
			->setHandler('throwException', function(\exception $exception) {
				return new phpunit\mock\definition\call\throwing($exception);
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
			->setHandler('assertStringStartsNotWith', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string($actual)->match('/^(?!' . preg_quote($expected, '/') . ')/', $failMessage);
			})
			->setHandler('assertStringEndsWith', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string($actual)->match('/' . preg_quote($expected, '/') . '$/', $failMessage);
			})
			->setHandler('assertStringEndsNotWith', function($expected, $actual, $failMessage = null) use ($self) {
				return $self->string($actual)->match('/(?<!' . preg_quote($expected, '/') . ')$/', $failMessage);
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

	protected function getUnsupportedHandler($handler)
	{
		$self = $this;

		return function() use ($self, $handler) {
			return $self->markTestSkipped($handler . ' is not supported');
		};
	}
}
