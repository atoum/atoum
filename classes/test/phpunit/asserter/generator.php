<?php
namespace mageekguy\atoum\test\phpunit\asserter;

use mageekguy\atoum\test\asserter;
use mageekguy\atoum;

class generator extends asserter\generator
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

	public function __construct(atoum\test $test, atoum\locale $locale = null, asserter\generator $generator = null)
	{
		$this->generator = $generator ?: new asserter\generator($test, $this->locale);

		parent::__construct($test, $locale);
	}

	public function setTest(atoum\test $test)
	{
		$this->generator->setTest($test);

		return parent::setTest($test);
	}

	public function setLocale(atoum\locale $locale = null)
	{
		$this->generator->setLocale($locale);

		return parent::setLocale($locale);
	}

	public function getAsserterClass($asserter)
	{
		$class = parent::getAsserterClass($asserter) ?: $this->generator->getAsserterClass($asserter);

		if($class === null && (preg_match('/^assert/i', $asserter) === 0 || in_array($asserter, $this->unsupportedAsserters)))
		{
			$this->test->skip(sprintf('%s is not supported', $asserter));
		}

		return $class;
	}

	public function setAlias($alias, $asserterClass)
	{
		$this->generator->setAlias($alias, $asserterClass);

		return parent::setAlias($alias, $asserterClass);
	}
} 