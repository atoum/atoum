<?php

namespace mageekguy\atoum\php;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\php\tokenizer
;

class tokenizer implements \iteratorAggregate
{
	protected $iterator = null;

	public function __construct()
	{
		$this->iterator = new tokenizer\iterator();
	}

	public function getIterator()
	{
		return $this->iterator;
	}

	public function resetIterator()
	{
		$this->iterator->reset();

		return $this;
	}

	public function tokenize($string)
	{
		$currentNamespace = null;
		$currentIterator = $this->iterator;

		foreach (token_get_all($string) as $token)
		{
			switch ($token[0])
			{
				case T_NAMESPACE:
					if ($currentNamespace !== null)
					{
						$currentIterator = $currentNamespace->getParent();
						$currentNamespace = null;
					}

					if ($currentNamespace === null)
					{
						$currentIterator->append($namespaceIterator = new tokenizer\iterator());
						$currentIterator = $currentNamespace = $namespaceIterator;
					}
					break;

				case T_CLOSE_TAG:
					if ($currentNamespace !== null)
					{
						$currentIterator = $currentNamespace->getParent();
						$currentNamespace = null;
					}
			}

			$currentIterator->append(new tokenizer\token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));
		}

		return $this;
	}
}

?>
