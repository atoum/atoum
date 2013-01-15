<?php

namespace mageekguy\atoum\mock\streams\file;

use
	mageekguy\atoum\mock\stream
;

class controller extends stream\controller
{
	public function canNotBeOpened()
	{
		return parent::__set('fopen', false);
	}

	public function canBeOpened()
	{
		return parent::__set('fopen', true);
	}

	public function canNotBeRead()
	{
		return parent::__set('stat', array('mode' => 32768));
	}

	public function canBeRead()
	{
		return parent::__set('stat', array('mode' => 33188));
	}

	public function canNotBeWrited()
	{
		return parent::__set('stat', array('uid' => getmyuid(), 'mode' => 33060));
	}

	public function canBeWrited()
	{
		return parent::__set('stat', array('uid' => getmyuid(), 'mode' => 33188));
	}
}
