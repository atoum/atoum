<?php

namespace mageekguy\atoum\mock\streams\fs;

use
	mageekguy\atoum\exceptions,
	mageekguy\atoum\mock\stream
;

class controller extends stream\controller
{
	protected $exists = true;
	protected $stats = array();

	public function __construct($path)
	{
		parent::__construct($path);

		$this->stats = array(
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

		$this->stats[0] = & $this->stats['dev'];
		$this->stats[1] = & $this->stats['ino'];
		$this->stats[2] = & $this->stats['mode'];
		$this->stats[3] = & $this->stats['nlink'];
		$this->stats[4] = & $this->stats['uid'];
		$this->stats[5] = & $this->stats['gid'];
		$this->stats[6] = & $this->stats['rdev'];
		$this->stats[7] = & $this->stats['size'];
		$this->stats[8] = & $this->stats['atime'];
		$this->stats[9] = & $this->stats['mtime'];
		$this->stats[10] = & $this->stats['ctime'];
		$this->stats[11] = & $this->stats['blksize'];
		$this->stats[12] = & $this->stats['blocks'];
	}

	public function exists()
	{
		$this->exists = true;

		return $this;
	}

	public function notExists()
	{
		$this->exists = false;

		return $this->clearStat();
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

	public function setMode($mode)
	{
		$this->stats['mode'] = $mode;

		return $this;
	}

	public function getMode()
	{
		return (int) sprintf('%03o', $this->stats['mode'] & 07777);
	}

	public function duplicate()
	{
		$controller = parent::duplicate();

		$controller->stats = & $this->stats;
		$controller->exists = & $this->exists;

		return $controller;
	}

	protected function stat()
	{
		return ($this->exists === false ? false : $this->stats);
	}

	protected function clearStat()
	{
		clearstatcache(false, $this->getPath());

		return $this;
	}

	protected function addPermission($permissions)
	{
		$this->stats['mode'] = $this->stats['mode'] | $permissions;

		return $this->clearStat();
	}

	protected function removePermissions($permissions)
	{
		$this->stats['mode'] = $this->stats['mode'] & ~ $permissions;

		return $this->clearStat();
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
		$permissions = $this->stats['mode'] & 07777;

		switch (true)
		{
			case getmyuid() === $this->stats['uid']:
				return ($permissions & $user) > 0;

			case getmygid() === $this->stats['gid']:
				return ($permissions & $group) > 0;

			default:
				return ($permissions & $other) > 0;
		}
	}
}
