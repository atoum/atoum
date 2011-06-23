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
		$currentDefaultValue = null;
		$currentIterator = $this->iterator;

		foreach ($tokens = new \arrayIterator(token_get_all($string)) as $key => $token)
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
						if (self::nextTokenIs(T_FUNCTION, $tokens) === true)
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
					else if ($currentNamespace !== null)
					{
						$currentIterator = $currentNamespace->getParent();
						$currentNamespace = null;
					}
					break;

				case ',':
					if ($currentDefaultValue !== null)
					{
						$currentIterator = $currentDefaultValue->getParent();
						$currentDefaultValue = null;
					}
					else if ($currentArgument !== null)
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

				case '=':
					if ($currentArgument !== null)
					{
						$this->appendCommentAndWhitespace($currentIterator, $tokens);

						$currentIterator->appendDefaultValue($currentDefaultValue = new iterators\phpDefaultValue());
						$currentIterator = $currentDefaultValue;
					}
					break;

				case T_ARRAY:
					if ($currentDefaultValue !== null)
					{
						$this->appendCommentAndWhitespace($currentIterator, $tokens);

						$tokens->next();

						if ($tokens->valid() === true)
						{
							$token = $tokens->current();

							if ($token[0] === '(')
							{
								$currentIterator->append(new tokenizer\token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));

								$stack = 1;

								while ($stack > 0 && $tokens->valid() === true)
								{
									$tokens->next();

									if ($tokens->valid() === true)
									{
										$token = $tokens->current();

										if ($token[0] === '(')
										{
											$stack++;
										}
										else if ($token[0] === ')')
										{
											$stack--;
										}

										$currentIterator->append(new tokenizer\token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));
									}
								}
							}
						}
					}
					break;

			}
		}

		return $this;
	}

	protected static function appendCommentAndWhitespace(tokenizer\iterator $iterator, \arrayIterator $tokens)
	{
		$key = $tokens->key();

		while (isset($tokens[$key + 1]) === true && ($tokens[$key + 1][0] === T_WHITESPACE || $tokens[$key + 1][0] === T_COMMENT))
		{
			$tokens->next();

			$token = $tokens->current();

			$iterator->append(new tokenizer\token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));

			$key = $tokens->key();
		}
	}

	protected static function nextTokenIs($tokenName, \arrayIterator $tokens)
	{
		$key = $tokens->key() + 1;

		while (isset($tokens[$key]) === true && ($tokens[$key] === T_WHITESPACE || $tokens[$key] === T_COMMENT))
		{
			$key++;
		}

		$key++;

		return (isset($tokens[$key]) === true && $tokens[$key][0] === $tokenName);
	}
}

?>
