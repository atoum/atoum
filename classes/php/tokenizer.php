<?php

namespace mageekguy\atoum\php;

use
	\mageekguy\atoum\exceptions,
	\mageekguy\atoum\php\tokenizer,
	\mageekguy\atoum\php\tokenizer\iterators
;

class tokenizer implements \iteratorAggregate
{
	protected $iterator = null;

	public function __construct()
	{
		$this->resetIterator();
	}

	public function getIterator()
	{
		return $this->iterator;
	}

	public function resetIterator()
	{
		$this->iterator = new iterators\phpScript();

		return $this;
	}

	public function tokenize($string)
	{
		$currentNamespace = null;
		$currentClass = null;
		$currentProperty = null;
		$currentMethod = null;
		$currentArgument = null;
		$currentIterator = $this->iterator;

		foreach ($tokens = token_get_all($string) as $key => $token)
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
						$currentIterator->appendNamespace($currentNamespace = new iterators\phpNamespace());
						$currentIterator = $currentNamespace;
					}
					break;

				case T_ABSTRACT:
					if ($currentClass === null)
					{
						$currentIterator->appendClass($currentClass = new iterators\phpClass());
						$currentIterator = $currentClass;
					}
					break;

				case T_FINAL:
					if ($currentClass === null)
					{
						$currentIterator->appendClass($currentClass = new iterators\phpClass());
						$currentIterator = $currentClass;
					}
					break;

				case T_CLASS:
					if ($currentClass === null)
					{
						$currentIterator->appendClass($currentClass = new iterators\phpClass());
						$currentIterator = $currentClass;
					}
					break;

				case T_VARIABLE:
					if ($currentMethod !== null && $currentArgument === null)
					{
						$currentIterator->appendArgument($currentArgument = new iterators\phpArgument());
						$currentIterator = $currentArgument;
					}
					else if ($currentClass !== null && $currentProperty === null)
					{
						$currentIterator->appendProperty($currentProperty = new iterators\phpProperty());
						$currentIterator = $currentProperty;
					}
					break;

				case T_FUNCTION:
					if ($currentClass !== null && $currentMethod === null)
					{
						$currentIterator->appendMethod($currentMethod = new iterators\phpMethod());
						$currentIterator = $currentMethod;
					}
					break;

				case T_PUBLIC:
				case T_PRIVATE:
				case T_PROTECTED:
					if ($currentClass !== null)
					{
						if (isset($tokens[$key + 1]) === true && $tokens[$key + 1][0] === T_WHITESPACE && isset($tokens[$key + 2]) === true && $tokens[$key + 2][0] === T_FUNCTION)
						{
							$currentIterator->appendMethod($currentMethod = new iterators\phpMethod());
							$currentIterator = $currentMethod;
						}
						else
						{
							$currentIterator->appendProperty($currentProperty = new iterators\phpProperty());
							$currentIterator = $currentProperty;
						}
					}
					break;

				case ';':
					if ($currentProperty !== null)
					{
						$currentIterator = $currentProperty->getParent();
						$currentProperty = null;
					}
					break;

				case ',':
					if ($currentArgument !== null)
					{
						$currentIterator = $currentArgument->getParent();
						$currentArgument = null;
					}
					else if ($currentProperty !== null)
					{
						$currentIterator = $currentProperty->getParent();
						$currentProperty = null;
					}
					break;

				case ')':
					if ($currentArgument !== null)
					{
						$currentIterator = $currentArgument->getParent();
						$currentArgument = null;
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
					if ($currentMethod !== null)
					{
						$currentIterator = $currentMethod->getParent();
						$currentMethod = null;
					}
					else if ($currentClass !== null)
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
