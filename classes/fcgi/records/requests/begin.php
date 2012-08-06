<?php

namespace mageekguy\atoum\fcgi\records\requests;

use
	mageekguy\atoum\fcgi,
	mageekguy\atoum\fcgi\records
;

class begin extends records\request
{
	const type = '1';
	const responder = '1';
	const authorizer = '2';
	const filter = '3';

	protected $role = '1';
	protected $persistentConnection = false;

	public function __construct($role = self::responder, $persistentConnection = false, $requestId = 1)
	{
		parent::__construct(self::type, $requestId);

		$this->setRole($role);

		if ($persistentConnection == true)
		{
			$this->setConnectionPersistent();
		}
	}

	public function setRole($role)
	{
		switch ($role)
		{
			case self::responder:
			case self::authorizer:
			case self::filter:
				$this->role = (string) $role;
				return $this;

			default:
				throw new fcgi\record\exception('Role \'' . $role . '\' is invalid');
		}
	}

	public function getRole()
	{
		return $this->role;
	}

	public function setConnectionPersistent()
	{
		$this->persistentConnection = true;

		return $this;
	}

	public function unsetConnectionPersistent()
	{
		$this->persistentConnection = false;

		return $this;
	}

	public function connectionIsPersistent()
	{
		return $this->persistentConnection;
	}

	public function getContentData()
	{
		list($roleB0, $roleB1) = self::toStreamValue($this->role);

		return sprintf('%c%c%c%c%c%c%c%c', $roleB0, $roleB1, ($this->persistentConnection ? 1 : 0), 0, 0, 0, 0, 0);
	}
}
