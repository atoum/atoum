<?php

namespace mageekguy\atoum\tests\functional\selenium;

class responseStatus
{
	const Success = 0;

	const NoSuchElement = 7;

	const NoSuchFrame = 8;

	const UnknownCommand = 9;

	const StaleElementReference = 10;

	const ElementNotVisible = 11;

	const InvalidElementState = 12;

	const UnknownError = 13;

	const ElementIsNotSelectable = 15;

	const JavaScriptError = 17;

	const XPathLookupError = 19;

	const NoSuchWindow = 23;

	const InvalidCookieDomain = 24;

	const UnableToSetCookie = 25;

	const Timeout = 28;

	protected function __construct() { }
}

?>
