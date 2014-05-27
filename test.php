<?php
namespace tests\units;

use mageekguy\atoum;
use mageekguy\atoum\adapter;
use mageekguy\atoum\annotations;
use mageekguy\atoum\asserter;
use mageekguy\atoum\test;

require_once __DIR__ . '/scripts/runner.php';

class creditcard extends atoum\asserters\string
{
	public function isValid($failMessage = null)
	{
		return $this->match('/(?:\d{4}){4}/', $failMessage ?: $this->_('%s is not a valid credit card number', $this));
	}

	public function __get($asserter)
	{
		switch (strtolower($asserter))
		{
			case 'isvalid':
				return $this->isValid();

			default:
				return parent::__get($asserter);
		}
	}
}

class stdClass extends atoum\test
{
	public function __construct(adapter $adapter = null, annotations\extractor $annotationExtractor = null, asserter\generator $asserterGenerator = null, test\assertion\manager $assertionManager = null, \closure $reflectionClassFactory = null)
	{
		parent::__construct($adapter, $annotationExtractor, $asserterGenerator, $assertionManager, $reflectionClassFactory);

		$this->getAsserterGenerator()->addNamespace('tests\units');

		$this
			->from('string')->use('isEqualTo')->as('equals')
			//->from('creditcard')->use('isValid')->as('valid')
		;
	}

	public function testFoo()
	{
		$this
			//->given($this->getAsserterGenerator()->addNamespace('tests\units'))

			//->string($u = uniqid())->equals($u)
			->creditcard('4444555566660000')->isValid
		;
	}
}
