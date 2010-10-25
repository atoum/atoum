<?php

namespace mageekguy\atoum;

class superglobal
{
	protected $superglobals = array();

	public function __set($superglobal, $value)
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
				$this->superglobals[$superglobal] = $value;
				return $this;

			default:
				throw new \runtimeException('PHP superglobal \'$' . $superglobal . '\' does not exist');
		}
	}

	public function & __get($superglobal)
	{
		switch ($superglobal)
		{
			case 'GLOBALS':
				if (array_key_exists($superglobal, $this->superglobals) === false)
				{
					return $GLOBALS;
				}
				else
				{
					return $this->superglobals[$superglobal];
				}

			case '_SERVER':
				if (array_key_exists($superglobal, $this->superglobals) === false)
				{
					return $_SERVER;
				}
				else
				{
					return $this->superglobals[$superglobal];
				}

			case '_GET':
				if (array_key_exists($superglobal, $this->superglobals) === false)
				{
					return $_GET;
				}
				else
				{
					return $this->superglobals[$superglobal];
				}

			case '_POST':
				if (array_key_exists($superglobal, $this->superglobals) === false)
				{
					return $_POST;
				}
				else
				{
					return $this->superglobals[$superglobal];
				}

			case '_FILES':
				if (array_key_exists($superglobal, $this->superglobals) === false)
				{
					return $_FILES;
				}
				else
				{
					return $this->superglobals[$superglobal];
				}

			case '_COOKIE':
				if (array_key_exists($superglobal, $this->superglobals) === false)
				{
					return $_COOKIE;
				}
				else
				{
					return $this->superglobals[$superglobal];
				}

			case '_SESSION':
				if (array_key_exists($superglobal, $this->superglobals) === false)
				{
					return $_SESSION;
				}
				else
				{
					return $this->superglobals[$superglobal];
				}

			case '_REQUEST':
				if (array_key_exists($superglobal, $this->superglobals) === false)
				{
					return $_REQUEST;
				}
				else
				{
					return $this->superglobals[$superglobal];
				}

			case '_ENV':
				if (array_key_exists($superglobal, $this->superglobals) === false)
				{
					return $_ENV;
				}
				else
				{
					return $this->superglobals[$superglobal];
				}

			default:
				throw new \runtimeException('PHP superglobal \'$' . $superglobal . '\' does not exist');
		}
	}
}

?>
