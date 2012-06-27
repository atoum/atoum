<?php

namespace mageekguy\atoum\tests\units;

use
	mageekguy\atoum
;

require_once __DIR__ . '/../runner.php';

class superglobals extends atoum\test
{
	public function test__get()
	{
		$superglobals = new atoum\superglobals();

		$this->assert
			->array->setByReferenceWith($superglobals->GLOBALS)->isReferenceTo($GLOBALS)
			->array->setByReferenceWith($superglobals->_SERVER)->isReferenceTo($_SERVER)
			->array->setByReferenceWith($superglobals->_GET)->isReferenceTo($_GET)
			->array->setByReferenceWith($superglobals->_POST)->isReferenceTo($_POST)
			->array->setByReferenceWith($superglobals->_FILES)->isReferenceTo($_FILES)
			->array->setByReferenceWith($superglobals->_COOKIE)->isReferenceTo($_COOKIE)
			->array->setByReferenceWith($superglobals->_SESSION)->isReferenceTo($_SESSION)
			->array->setByReferenceWith($superglobals->_REQUEST)->isReferenceTo($_REQUEST)
			->array->setByReferenceWith($superglobals->_ENV)->isReferenceTo($_ENV)
		;
	}

	public function test__set()
	{
		$superglobals = new atoum\superglobals();

		$this->assert
			->exception(function() use ($superglobals, & $name) {
						$superglobals->{$name = uniqid()} = uniqid();
					}
				)
				->isInstanceOf('mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('PHP superglobal \'$' . $name . '\' does not exist')
		;

		$superglobals->GLOBALS = ($variable = uniqid());

		$this->assert
			->string($superglobals->GLOBALS)->isEqualTo($variable)
		;

		$superglobals->_SERVER = ($variable = uniqid());

		$this->assert
			->string($superglobals->_SERVER)->isEqualTo($variable)
		;

		$superglobals->_GET = ($variable = uniqid());

		$this->assert
			->string($superglobals->_GET)->isEqualTo($variable)
		;

		$superglobals->_POST = ($variable = uniqid());

		$this->assert
			->string($superglobals->_POST)->isEqualTo($variable)
		;

		$superglobals->_FILES = ($variable = uniqid());

		$this->assert
			->string($superglobals->_FILES)->isEqualTo($variable)
		;

		$superglobals->_COOKIE = ($variable = uniqid());

		$this->assert
			->string($superglobals->_COOKIE)->isEqualTo($variable)
		;

		$superglobals->_SESSION = ($variable = uniqid());

		$this->assert
			->string($superglobals->_SESSION)->isEqualTo($variable)
		;

		$superglobals->_REQUEST = ($variable = uniqid());

		$this->assert
			->string($superglobals->_REQUEST)->isEqualTo($variable)
		;

		$superglobals->_ENV = ($variable = uniqid());

		$this->assert
			->string($superglobals->_ENV)->isEqualTo($variable)
		;
	}
}
