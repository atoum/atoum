<?php

namespace atoum\atoum\php;

use atoum\atoum\php\tokenizer\iterators;
use atoum\atoum\php\tokenizer\token;

class tokenizer implements \iteratorAggregate
{
    protected $iterator = null;

    private $tokens = null;
    private $currentIterator = null;
    private $currentNamespace = null;
    private $currentImportation = null;
    private $currentFunction = null;

    public function __construct()
    {
        $this->resetIterator();
    }

    #[\ReturnTypeWillChange]
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

        foreach ($this->tokens = new \arrayIterator(token_get_all($string)) as $token) {
            switch ($token[0]) {
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

            if ($token === null) {
                continue;
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

        while ($inImportation === true) {
            $token = $this->tokens->current();

            switch ($token[0]) {
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

        while ($inNamespace === true) {
            $token = $this->tokens->current();

            switch ($token[0]) {
                case T_NAMESPACE:
                    $parent = $this->currentIterator->getParent();

                    if ($parent !== null) {
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
                    if ($this->nextTokenIs(T_OPEN_TAG) === false) {
                        $this->currentIterator = $this->currentIterator->getParent();
                        $inNamespace = false;
                    }
                    break;

                case '}':
                    $inNamespace = false;
                    break;
            }

            $this->currentIterator->append(new token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));

            if ($token[0] === '}') {
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

        while ($inFunction === true) {
            $token = $this->tokens->current();

            switch ($token[0]) {
                case '}':
                    $inFunction = false;
                    break;
            }

            $this->currentIterator->append(new token($token[0], isset($token[1]) === false ? null : $token[1], isset($token[2]) === false ? null : $token[2]));

            if ($token[0] === '}') {
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

        while ($inConstant === true) {
            $token = $this->tokens->current();

            switch ($token[0]) {
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

    private function nextTokenIs($tokenName, array $skipedTags = [T_WHITESPACE, T_COMMENT, T_INLINE_HTML])
    {
        $key = $this->tokens->key() + 1;

        while (isset($this->tokens[$key]) === true && in_array($this->tokens[$key], $skipedTags) === true) {
            $key++;
        }

        $key++;

        return (isset($this->tokens[$key]) === true && $this->tokens[$key][0] === $tokenName);
    }
}
