<?php
namespace mageekguy\atoum\test\phpunit\asserter;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\test\assertion
;

class generator extends atoum\test\asserter\generator
{
	const defaultAsserterNamespace = 'mageekguy\atoum\test\phpunit\asserters';

	private $unsupportedAsserters = array(
		//'setExpectedException',
		//'markTestIncomplete',
		//'isInstanceOf',
		//'matchesRegularExpression',
		//'stringContains',
		//'lessThan',
		//'lessThanOrEqual',
		//'greaterThan',
		//'greaterThanOrEqual',
		//'fileExists',
		//'isType',
		'assertThat',
		'assertTag',
		//'containsOnlyInstancesOf',
		//'classHasAttribute',
		//'containsOnly',
		//'contains',
		//'anything',
		//'attributeEqualTo',
		'assertJsonFileEqualsJsonFile',
		'assertJsonFileNotEqualsJsonFile',
		'assertJsonStringNotEqualsJsonFile',
		'assertJsonStringEqualsJsonFile',
		'assertJsonStringNotEqualsJsonString',
		'assertJsonStringEqualsJsonString',
		'assertXmlFileEqualsXmlFile',
		'assertXmlFileNotEqualsXmlFile',
		'assertXmlStringEqualsXmlFile',
		'assertXmlStringNotEqualsXmlFile',
		'assertXmlStringEqualsXmlString',
		'assertXmlStringNotEqualsXmlString',
		'assertEqualXMLStructure',
		'assertObjectHasAttribute',
		'assertObjectNotHasAttribute',
		'assertNotRegExp',
		'assertAttributeGreaterThan',
		'assertAttributeGreaterThanOrEqual',
		'assertAttributeLessThan',
		'assertLessThanOrEqual',
		'assertAttributeLessThanOrEqual',
		//'readAttribute',
		'assertAttributeContains',
		'assertAttributeContainsOnly',
		'assertAttributeNotContains',
		'assertAttributeNotContainsOnly',
		'assertAttributeContains',
		'assertAttributeNotContains',
		'assertAttributeNotEquals',
		'assertAttributeSame',
		'assertAttributeNotSame',
		'assertClassHasAttribute',
		'assertClassNotHasAttribute',
		'assertClassHasStaticAttribute',
		'assertClassNotHasStaticAttribute',
		'assertObjectHasAttribute',
		'assertObjectNotHasAttribute',
		'assertAttributeEmpty',
		'assertAttributeNotEmpty',
		//'attribute',
		//'classHasStaticAttribute',
		//'objectHasAttribute',
		'assertNotTag',
		'assertSelectCount',
		'assertSelectEquals',
		'assertSelectRegExp',
		'assertFileNotEquals',
		'assertStringNotEqualsFile',
		'assertStringMatchesFormat',
		'assertStringNotMatchesFormat'
	);

	protected $generator;

	public function __construct(atoum\test $test, asserter\resolver $resolver = null, assertion\aliaser $aliaser = null)
	{
		$resolver = new asserter\resolver();
		$resolver->addNamespace(self::defaultAsserterNamespace);

		parent::__construct($test, $resolver, $aliaser);
	}

	/*public function setTest(atoum\test $test)
	{
		$this->generator->setTest($test);

		return parent::setTest($test);
	}

	public function setLocale(atoum\locale $locale = null)
	{
		$this->generator->setLocale($locale);

		return parent::setLocale($locale);
	}*/

	public function getAsserterClass($asserter)
	{
		$class = parent::getAsserterClass($asserter);

		if($class === null && (preg_match('/^assert/i', $asserter) === 0 || in_array($asserter, $this->unsupportedAsserters)))
		{
			$this->test->skip(sprintf('%s is not supported', $asserter));
		}

		return $class;
	}

	/*public function setAlias($alias, $asserterClass)
	{
		$this->generator->setAlias($alias, $asserterClass);

		return parent::setAlias($alias, $asserterClass);
	}*/
} 
