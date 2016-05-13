<?php

namespace mageekguy\atoum\php;

use
	mageekguy\atoum\exceptions,
	mageekguy\atoum\php\tokenizer\token,
	mageekguy\atoum\php\tokenizer\iterators
;

class tokenizer implements \iteratorAggregate
{
	protected $iterator = null;

	private $tokens = null;
	private $currentIterator = null;

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
		$this->currentIterator = $this->iterator;

		foreach ($this->tokens = new \arrayIterator(token_get_all($string)) as $key => $token)
		{
			switch ($token[0])
			{
				case T_CONST:
					$token = $this->appendConstant();
					break;

				case T_USE:
					$token = $this->appendImportation();
					break;

				case T_NAMESPACE:
					$token = $this->appendNamespace();
					break;

				case T_FUNCTION:
					$token = $this->appendFunction();
					break;
			}

			$this->currentIterator->append(new token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));
		}

		return $this;
	}

	private function appendImportation()
	{
		$this->currentIterator->appendImportation($this->currentImportation = new iterators\phpImportation());
		$this->currentIterator = $this->currentImportation;

		$inImportation = true;

		while ($inImportation === true)
		{
			$token = $this->tokens->current();

			switch ($token[0])
			{
				case ';':
				case T_CLOSE_TAG:
					$this->currentIterator = $this->currentIterator->getParent();
					$inImportation = false;
					break;

				default:
					$this->currentIterator->append(new token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));
					$this->tokens->next();
			}

			$inImportation = $inImportation && $this->tokens->valid();
		}

		return $this->tokens->valid() === false ? null : $this->tokens->current();
	}

	private function appendNamespace()
	{
		$inNamespace = true;

		while ($inNamespace === true)
		{
			$token = $this->tokens->current();

			switch ($token[0])
			{
				case T_NAMESPACE:
					$parent = $this->currentIterator->getParent();

					if ($parent !== null)
					{
						$this->currentIterator = $parent;
					}

					$this->currentIterator->appendNamespace($this->currentNamespace = new iterators\phpNamespace());
					$this->currentIterator = $this->currentNamespace;
					break;


				case T_CONST:
					$this->appendConstant();
					break;

				case T_FUNCTION:
					$this->appendFunction();
					break;

				case T_FINAL:
				case T_ABSTRACT:
				case T_CLASS:
					$this->appendClass();
					break;

				case T_INTERFACE:
					$this->appendInterface();
					break;

				case ';':
					$this->currentIterator = $this->currentIterator->getParent();
					$inNamespace = false;
					break;

				case T_CLOSE_TAG:
					if ($this->nextTokenIs(T_OPEN_TAG) === false)
					{
						$this->currentIterator = $this->currentIterator->getParent();
						$inNamespace = false;
					}
					break;

				case '}':
					$inNamespace = false;
					break;
			}

			$this->currentIterator->append(new token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));

			if ($token[0] === '}')
			{
				$this->currentIterator = $this->currentIterator->getParent();
			}

			$this->tokens->next();

			$inNamespace = $inNamespace && $this->tokens->valid();
		}

		return $this->tokens->valid() === false ? null : $this->tokens->current();
	}

	private function appendFunction()
	{
		$inFunction = true;

		$this->currentIterator->appendFunction($this->currentFunction = new iterators\phpFunction());
		$this->currentIterator = $this->currentFunction;

		while ($inFunction === true)
		{
			$token = $this->tokens->current();

			switch ($token[0])
			{
				case '}':
					$inFunction = false;
					break;
			}

			$this->currentIterator->append(new token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));

			if ($token[0] === '}')
			{
				$this->currentIterator = $this->currentIterator->getParent();
			}

			$this->tokens->next();

			$inFunction = $inFunction && $this->tokens->valid();
		}

		return $this->tokens->valid() === false ? null : $this->tokens->current();
	}

	private function appendConstant()
	{
		$this->currentIterator->appendConstant($this->currentNamespace = new iterators\phpConstant());
		$this->currentIterator = $this->currentNamespace;

		$inConstant = true;

		while ($inConstant === true)
		{
			$token = $this->tokens->current();

			switch ($token[0])
			{
				case ';':
				case T_CLOSE_TAG:
					$this->currentIterator = $this->currentIterator->getParent();
					$inConstant = false;
					break;

				default:
					$this->currentIterator->append(new token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));
					$this->tokens->next();
			}

			$inConstant = $inConstant && $this->tokens->valid();
		}

		return $this->tokens->valid() === false ? null : $this->tokens->current();
	}

	private function appendCommentAndWhitespace()
	{
		$key = $this->tokens->key();

		while (isset($this->tokens[$key + 1]) === true && ($this->tokens[$key + 1][0] === T_WHITESPACE || $this->tokens[$key + 1][0] === T_COMMENT))
		{
			$this->tokens->next();

			$token = $this->tokens->current();

			$this->currentIterator->append(new token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));

			$key = $this->tokens->key();
		}
	}

	private function appendArray()
	{
		$this->appendCommentAndWhitespace();

		$this->tokens->next();

		if ($this->tokens->valid() === true)
		{
			$token = $this->tokens->current();

			if ($token[0] === '(')
			{
				$this->currentIterator->append(new token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));

				$stack = 1;

				while ($stack > 0 && $this->tokens->valid() === true)
				{
					$this->tokens->next();

					if ($this->tokens->valid() === true)
					{
						$token = $this->tokens->current();

						if ($token[0] === '(')
						{
							$stack++;
						}
						else if ($token[0] === ')')
						{
							$stack--;
						}

						$this->currentIterator->append(new token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));
					}
				}
			}
		}
	}

	private function nextTokenIs($tokenName, array $skipedTags = array(T_WHITESPACE, T_COMMENT, T_INLINE_HTML))
	{
		$key = $this->tokens->key() + 1;

		while (isset($this->tokens[$key]) === true && in_array($this->tokens[$key], $skipedTags) === true)
		{
			$key++;
		}

		$key++;

		return (isset($this->tokens[$key]) === true && $this->tokens[$key][0] === $tokenName);
	}
}
