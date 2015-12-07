<?php

namespace mageekguy\atoum\test\phpunit\asserters;

use
	mageekguy\atoum,
	mageekguy\atoum\asserter,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class assertContainsOnly extends asserter
{
	public function setWithArguments(array $arguments)
	{
		parent::setWithArguments($arguments);

		foreach ($arguments[1] as $item)
		{
			try
			{
				switch ($arguments[0])
				{
					case 'int':
					case 'integer':
						$asserter = new asserters\integer();
						break;

					case 'float':
						$asserter = new asserters\integer();
						break;

					case 'string':
						$asserter = new asserters\string();
						break;

					case 'bool':
					case 'boolean':
						$asserter = new asserters\boolean();
						break;

					case 'array':
						$asserter = new asserters\phpArray();
						break;

					default:
						$asserter = new asserters\object();

						$asserter->setWith($item)->isInstanceOf($arguments[0]);
						break;
				}

				if ($asserter->getValue() === null)
				{
					$asserter->setWith($item);
				}
			}
			catch(atoum\asserter\exception $exception)
			{
				$this->fail(isset($arguments[2]) ? $arguments[2] : $exception->getMessage());
			}
		}

		$this->pass();

		return $this;
	}
} 