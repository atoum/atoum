<?php

namespace mageekguy\atoum\mock\streams\fs;

use
	mageekguy\atoum,
	mageekguy\atoum\mock\stream
;

class controller extends stream\controller
{
	protected $adapter = null;
	protected $exists = true;
	protected $stat = array();

	public function __construct($path, atoum\adapter $adapter = null)
	{
		parent::__construct($path);

		$this->setAdapter($adapter)->stat = array(
			'dev' => 0,
			'ino' => 0,
			'mode' => 0,
			'nlink' => 0,
			'uid' => getmyuid(),
			'gid' => getmygid(),
			'rdev' => 0,
			'size' => 0,
			'atime' => 507769200,
			'mtime' => 507769200,
			'ctime' => 507769200,
			'blksize' => 0,
			'blocks' => 0
		);

		$this->stat[0] = & $this->stat['dev'];
		$this->stat[1] = & $this->stat['ino'];
		$this->stat[2] = & $this->stat['mode'];
		$this->stat[3] = & $this->stat['nlink'];
		$this->stat[4] = & $this->stat['uid'];
		$this->stat[5] = & $this->stat['gid'];
		$this->stat[6] = & $this->stat['rdev'];
		$this->stat[7] = & $this->stat['size'];
		$this->stat[8] = & $this->stat['atime'];
		$this->stat[9] = & $this->stat['mtime'];
		$this->stat[10] = & $this->stat['ctime'];
		$this->stat[11] = & $this->stat['blksize'];
		$this->stat[12] = & $this->stat['blocks'];
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function setAdapter(atoum\adapter $adapter = null)
	{
		$this->adapter = $adapter ?: new atoum\adapter();

		return $this;
	}

	public function exists()
	{
		$this->exists = true;

		return $this->clearStatCache();
	}

	public function notExists()
	{
		$this->exists = false;

		return $this->clearStatCache();
	}

	public function isNotReadable()
	{
		return $this->removePermissions(0444);
	}

	public function isReadable()
	{
		return $this->addPermission(0444);
	}

	public function isNotWritable()
	{
		return $this->removePermissions(0222);
	}

	public function isWritable()
	{
		return $this->addPermission(0222);
	}

	public function isNotExecutable()
	{
		return $this->removePermissions(0111);
	}

	public function isExecutable()
	{
		return $this->addPermission(0111);
	}

	public function setPermissions($permissions)
	{
		return $this->setStat('mode', $permissions);
	}

	public function getPermissions()
	{
		return ($this->exists === false ? null : (int) sprintf('%03o', $this->stat['mode'] & 07777));
	}

	public function duplicate()
	{
		$controller = parent::duplicate();

		$controller->adapter = & $this->adapter;
		$controller->exists = & $this->exists;
		$controller->stat = & $this->stat;

		return $controller;
	}

	public function getStat()
	{
		return ($this->exists === false ? false : $this->stat);
	}

	public function stream_stat()
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return $this->invoke(__FUNCTION__, array());
		}
		else
		{
			$this->addCall(__FUNCTION__, array());

			return $this->getStat();
		}
	}

	public function url_stat($path, $flags)
	{
		if ($this->nextCallIsOverloaded(__FUNCTION__) === true)
		{
			return $this->invoke(__FUNCTION__, func_get_args());
		}
		else
		{
			$this->addCall(__FUNCTION__, func_get_args());

			return $this->getStat();
		}
	}

	protected function setStat($name, $value)
	{
		if (isset($this->stat[$name]) === true)
		{
			$this->stat[$name] = $value;

			$this->clearStatCache();
		}

		return $this;
	}

	protected function clearStatCache()
	{
		$this->adapter->clearstatcache(false, $this->getPath());

		return $this;
	}

	protected function addPermission($permissions)
	{
		return $this->setStat('mode', $this->stat['mode'] | $permissions);
	}

	protected function removePermissions($permissions)
	{
		return $this->setStat('mode', $this->stat['mode'] & ~ $permissions);
	}

	protected function checkIfReadable()
	{
		return $this->checkPermission(0400, 0040, 0004);
	}

	protected function checkIfWritable()
	{
		return $this->checkPermission(0200, 0020, 0002);
	}

	protected function checkPermission($user, $group, $other)
	{
		$permissions = $this->stat['mode'] & 07777;

		switch (true)
		{
			case getmyuid() === $this->stat['uid']:
				return ($permissions & $user) > 0;

			case getmygid() === $this->stat['gid']:
				return ($permissions & $group) > 0;

			default:
				return ($permissions & $other) > 0;
		}
	}
}
