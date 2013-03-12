<?php

namespace mageekguy\atoum\mock\streams\fs\directory;

use
	mageekguy\atoum\exceptions,
	mageekguy\atoum\mock\stream
;

class controller extends stream\controller
{
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

		$this->setMode('755');
	}

	public function setMode($mode)
	{
		$this->stats['mode'] = 0400000 | octdec($mode);

		return $this;
	}

	public function getMode()
	{
		return (int) sprintf('%03o', $this->stats['mode'] & 07777);
	}
}
