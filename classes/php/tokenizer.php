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
		$currentClass = null;
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
						$currentIterator->append($currentNamespace = new tokenizer\iterator());
						$currentIterator = $currentNamespace;
					}
					break;

				case T_CLASS:
					if ($currentClass === null)
					{
						$currentIterator->append($currentClass = new tokenizer\iterator());
						$currentIterator = $currentClass;
					}
					break;

				case T_CLOSE_TAG:
					if ($currentClass !== null)
					{
						$currentIterator = $currentClass->getParent();
						$currentClass = null;
					}

					if ($currentNamespace !== null)
					{
						$currentIterator = $currentNamespace->getParent();
						$currentNamespace = null;
					}
					break;
			}

			$currentIterator->append(new tokenizer\token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));

			switch ($token[0])
			{
				case '}':
					if ($currentClass !== null)
					{
						$currentIterator = $currentClass->getParent();
						$currentClass = null;
					}
					break;
			}
		}

		return $this;
	}
}

?>
