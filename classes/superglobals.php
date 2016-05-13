<?php

namespace mageekguy\atoum;

class superglobals
{
	protected $superglobals = array();

	public function __set($superglobal, $value)
	{
		$this->check($superglobal)->superglobals[$superglobal] = $value;
	}

	public function & __get($superglobal)
	{
		$this->check($superglobal);

		if (array_key_exists($superglobal, $this->superglobals) === true)
		{
			return $this->superglobals[$superglobal];
		}
		else switch ($superglobal)
		{
			case 'GLOBALS':
				return $GLOBALS;

			case '_SERVER':
				return $_SERVER;

			case '_GET':
				return $_GET;

			case '_POST':
				return $_POST;

			case '_FILES':
				return $_FILES;

			case '_COOKIE':
				return $_COOKIE;

			case '_SESSION':
				return $_SESSION;

			case '_REQUEST':
				return $_REQUEST;

			case '_ENV':
				return $_ENV;
		}
	}

	protected function check($superglobal)
	{
		switch ($superglobal)
		{
			case 'GLOBALS':
			case '_SERVER':
			case '_GET':
			case '_POST':
			case '_FILES':
			case '_COOKIE':
			case '_SESSION':
			case '_REQUEST':
			case '_ENV':
				return $this;

			default:
				throw new exceptions\logic\invalidArgument('PHP superglobal \'$' . $superglobal . '\' does not exist');
		}
	}
}
