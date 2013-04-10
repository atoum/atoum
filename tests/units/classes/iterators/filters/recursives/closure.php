<?php

namespace mageekguy\atoum\tests\units\iterators\filters\recursives;

require __DIR__ . '/../../../../runner.php';

use
	mageekguy\atoum,
	mageekguy\atoum\mock,
	mageekguy\atoum\iterators\filters\recursives
;

class closure extends atoum\test
{
	public function testDeepFilter()
	{
		$array = array(
			0,
			1,
			2,
			array(
				3,
				array(
					4,
					5
				),
				6
			),
			7,
			8,
			9
		);

		$this
			->if($iterator = new \recursiveIteratorIterator(
					new recursives\closure(
						new \recursiveArrayIterator($array),
						function($current, $key, \recursiveArrayIterator $innerIterator)
						{
							if (true === $innerIterator->hasChildren())
							{
								return true;
							}

							return (0 === $current % 2);
						}
					)
				)
			)
			->then
				->array(iterator_to_array($iterator, false))
				->hasSize(5)
				->strictlyContainsValues(array(0, 2, 4, 6, 8))
				->strictlyNotContainsValues(array(1, 3, 5, 7, 9));
	}
}
