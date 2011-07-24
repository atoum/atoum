<?php

namespace mageekguy\atoum\tests\units\tests\functional\selenium;

use
	mageekguy\atoum,
	mageekguy\atoum\tests\functional\selenium  as s
;

require_once(__DIR__ . '/../../../runner.php');

class by extends atoum\test
{
	public function testByClassName()
	{
		$value = "my_css_class";
		$by = s\by::className($value);

		$this->assert->object($by)->isInstanceOf('mageekguy\atoum\tests\functional\selenium\by');
		$this->assert->string((string)$by)->isEqualTo("{'using':'class name', 'value':'" . $value . "'}");
	}

	public function testByCssSelector()
	{
		$value = ".my > .css #selector";
		$by = s\by::cssSelector($value);

		$this->assert->object($by)->isInstanceOf('mageekguy\atoum\tests\functional\selenium\by');
		$this->assert->string((string)$by)->isEqualTo("{'using':'css selector', 'value':'" . $value . "'}");
	}

	public function testById()
	{
		$value = "#my_id";
		$by = s\by::Id($value);

		$this->assert->object($by)->isInstanceOf('mageekguy\atoum\tests\functional\selenium\by');
		$this->assert->string((string)$by)->isEqualTo("{'using':'id', 'value':'" . $value . "'}");
	}

	public function testByName()
	{
		$value = "my_name";
		$by = s\by::name($value);

		$this->assert->object($by)->isInstanceOf('mageekguy\atoum\tests\functional\selenium\by');
		$this->assert->string((string)$by)->isEqualTo("{'using':'name', 'value':'" . $value . "'}");
	}

	public function testByLinkText()
	{
		$value = "my link text";
		$by = s\by::linkText($value);

		$this->assert->object($by)->isInstanceOf('mageekguy\atoum\tests\functional\selenium\by');
		$this->assert->string((string)$by)->isEqualTo("{'using':'link text', 'value':'" . $value . "'}");
	}

	public function testByPartialLinkText()
	{
		$value = "my partial";
		$by = s\by::partialLinkText($value);

		$this->assert->object($by)->isInstanceOf('mageekguy\atoum\tests\functional\selenium\by');
		$this->assert->string((string)$by)->isEqualTo("{'using':'partial link text', 'value':'" . $value . "'}");
	}

	public function testByTagName()
	{
		$value = "div";
		$by = s\by::tagName($value);

		$this->assert->object($by)->isInstanceOf('mageekguy\atoum\tests\functional\selenium\by');
		$this->assert->string((string)$by)->isEqualTo("{'using':'tag name', 'value':'" . $value . "'}");
	}

	public function testByXpath()
	{
		$value = "div[@id='test']";
		$by = s\by::xpath($value);

		$this->assert->object($by)->isInstanceOf('mageekguy\atoum\tests\functional\selenium\by');
		$this->assert->string((string)$by)->isEqualTo("{'using':'xpath', 'value':'" . $value . "'}");
	}
}

?>
