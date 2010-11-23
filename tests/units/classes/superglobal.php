<?php

namespace mageekguy\atoum\tests\units;

use \mageekguy\atoum;

require_once(__DIR__ . '/../runner.php');

class superglobal extends atoum\test
{
	public function test__get()
	{
		$superglobal = new atoum\superglobal();

		$this->assert
			->array->setByReferenceWith($superglobal->GLOBALS)->isReferenceTo($GLOBALS)
			->array->setByReferenceWith($superglobal->_SERVER)->isReferenceTo($_SERVER)
			->array->setByReferenceWith($superglobal->_GET)->isReferenceTo($_GET)
			->array->setByReferenceWith($superglobal->_POST)->isReferenceTo($_POST)
			->array->setByReferenceWith($superglobal->_FILES)->isReferenceTo($_FILES)
			->array->setByReferenceWith($superglobal->_COOKIE)->isReferenceTo($_COOKIE)
			->array->setByReferenceWith($superglobal->_SESSION)->isReferenceTo($_SESSION)
			->array->setByReferenceWith($superglobal->_REQUEST)->isReferenceTo($_REQUEST)
			->array->setByReferenceWith($superglobal->_ENV)->isReferenceTo($_ENV)
		;
	}

	public function test__set()
	{
		$superglobal = new atoum\superglobal();

		$this->assert
			->exception(function() use ($superglobal, & $name) {
						$superglobal->{$name = uniqid()} = uniqid();
					}
				)
				->isInstanceOf('\mageekguy\atoum\exceptions\logic\invalidArgument')
				->hasMessage('PHP superglobal \'$' . $name . '\' does not exist')
		;

		$superglobal->GLOBALS = ($variable = uniqid());

		$this->assert
			->string($superglobal->GLOBALS)->isEqualTo($variable)
		;

		$superglobal->_SERVER = ($variable = uniqid());

		$this->assert
			->string($superglobal->_SERVER)->isEqualTo($variable)
		;

		$superglobal->_GET = ($variable = uniqid());

		$this->assert
			->string($superglobal->_GET)->isEqualTo($variable)
		;

		$superglobal->_POST = ($variable = uniqid());

		$this->assert
			->string($superglobal->_POST)->isEqualTo($variable)
		;

		$superglobal->_FILES = ($variable = uniqid());

		$this->assert
			->string($superglobal->_FILES)->isEqualTo($variable)
		;

		$superglobal->_COOKIE = ($variable = uniqid());

		$this->assert
			->string($superglobal->_COOKIE)->isEqualTo($variable)
		;

		$superglobal->_SESSION = ($variable = uniqid());

		$this->assert
			->string($superglobal->_SESSION)->isEqualTo($variable)
		;

		$superglobal->_REQUEST = ($variable = uniqid());

		$this->assert
			->string($superglobal->_REQUEST)->isEqualTo($variable)
		;

		$superglobal->_ENV = ($variable = uniqid());

		$this->assert
			->string($superglobal->_ENV)->isEqualTo($variable)
		;
	}
}

?>
