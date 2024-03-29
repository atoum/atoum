<?php

namespace atoum\atoum\tests\units;

use atoum\atoum;
use atoum\atoum\superglobals as testedClass;

require_once __DIR__ . '/../runner.php';

class superglobals extends atoum\test
{
    public function test__get()
    {
        if (isset($GLOBALS['_SESSION']) === false) {
            $GLOBALS['_SESSION'] = [];
        }

        $this
            ->if($superglobals = new testedClass())
            ->then
                ->array->setByReferenceWith($superglobals->_SERVER)->isReferenceTo($_SERVER)
                ->array->setByReferenceWith($superglobals->_GET)->isReferenceTo($_GET)
                ->array->setByReferenceWith($superglobals->_POST)->isReferenceTo($_POST)
                ->array->setByReferenceWith($superglobals->_FILES)->isReferenceTo($_FILES)
                ->array->setByReferenceWith($superglobals->_COOKIE)->isReferenceTo($_COOKIE)
                ->array->setByReferenceWith($superglobals->_REQUEST)->isReferenceTo($_REQUEST)
                ->array->setByReferenceWith($superglobals->_ENV)->isReferenceTo($_ENV)
        ;

        if (isset($_SESSION)) {
            $this->assert()->array->setByReferenceWith($superglobals->_SESSION)->isReferenceTo($_SESSION);
        } else {
            $this->assert()->array->setByReferenceWith($superglobals->_SESSION)->isReferenceTo($superglobals->_SESSION);
        }
    }

    public function test__set()
    {
        $this
            ->if($superglobals = new testedClass())
            ->then
                ->exception(function () use ($superglobals, & $name) {
                    $superglobals->{$name = uniqid()} = uniqid();
                })
                    ->isInstanceOf(atoum\exceptions\logic\invalidArgument::class)
                    ->hasMessage('PHP superglobal \'$' . $name . '\' does not exist')
            ->if($superglobals->GLOBALS = ($variable = uniqid()))
            ->then
                ->string($superglobals->GLOBALS)->isEqualTo($variable)
            ->if($superglobals->_SERVER = ($variable = uniqid()))
            ->then
                ->string($superglobals->_SERVER)->isEqualTo($variable)
            ->if($superglobals->_GET = ($variable = uniqid()))
            ->then
                ->string($superglobals->_GET)->isEqualTo($variable)
            ->if($superglobals->_POST = ($variable = uniqid()))
            ->then
                ->string($superglobals->_POST)->isEqualTo($variable)
            ->if($superglobals->_FILES = ($variable = uniqid()))
            ->then
                ->string($superglobals->_FILES)->isEqualTo($variable)
            ->if($superglobals->_COOKIE = ($variable = uniqid()))
            ->then
                ->string($superglobals->_COOKIE)->isEqualTo($variable)
            ->if($superglobals->_SESSION = ($variable = uniqid()))
            ->then
                ->string($superglobals->_SESSION)->isEqualTo($variable)
            ->if($superglobals->_REQUEST = ($variable = uniqid()))
            ->then
                ->string($superglobals->_REQUEST)->isEqualTo($variable)
            ->if($superglobals->_ENV = ($variable = uniqid()))
            ->then
                ->string($superglobals->_ENV)->isEqualTo($variable)
        ;
    }
}
