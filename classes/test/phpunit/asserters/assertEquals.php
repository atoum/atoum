<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\test\phpunit\asserter
;

class assertEquals extends asserter
{
	public function setWithArguments(array $arguments)
	{
		parent::setWithArguments($arguments);

		$arguments = array_replace(
			array(
				2 => '',
				3 => null,
				4 => 10,
				5 => false,
				6 => false,
				7 => null,
			),
			$arguments
		);

		$assertion = function($asserter, $actual, $expected) {
			$asserter->setWith($actual)->isEqualTo($expected);
		};

		switch (true)
		{
			case $arguments[1] instanceof \DOMNode && $arguments[0] instanceof \DOMNode:
				$asserter = new asserters\dom();
				break;

			case is_object($arguments[1]) && is_object($arguments[0]):
				$asserter = new asserters\object();
				break;

			case is_int($arguments[1]) && is_int($arguments[0]) && $arguments[3] === null:
				$asserter = new asserters\integer();
				break;

			case (is_float($arguments[1]) && is_float($arguments[0])) || (is_int($arguments[1]) && is_int($arguments[0])):
				$asserter = new asserters\phpFloat();

				$assertion = function($asserter, $actual, $expected) use ($arguments) {
					$asserter->setWith((float) $actual)->isNearlyEqualTo((float) $expected, $arguments[3]);
				};
				break;

			case is_bool($arguments[1]) && is_bool($arguments[0]):
				$asserter = new asserters\boolean();
				break;

			case is_array($arguments[1]) && is_array($arguments[0]):
				sort($arguments[0]);
				sort($arguments[1]);

				$asserter = new asserters\phpArray();
				break;

			case is_string($arguments[1]) || is_string($arguments[0]):
				if ($arguments[6] === true)
				{
					$arguments[0] = strtolower($arguments[0]);
					$arguments[1] = strtolower($arguments[1]);
				}

				$asserter = new asserters\phpString();

				$assertion = function($asserter, $actual, $expected) use ($arguments) {
					$asserter->setWith((string) $actual)->isEqualTo((string) $expected, $arguments[3]);
				};
				break;

			default:
				$asserter = new asserters\variable();
		}

		try
		{
			$assertion($asserter, $arguments[1], $arguments[0]);

			$this->pass();
		}
		catch(atoum\asserter\exception $exception)
		{
			$this->fail(isset($arguments[7]) ? $arguments[7] : $exception->getMessage());
		}


		return $this;
	}
} 
