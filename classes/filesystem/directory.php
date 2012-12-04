<?php
namespace mageekguy\atoum\filesystem;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\mock\stream
;

class directory extends node
{
	public function __construct($name = null, node $parent = null)
	{
		parent::__construct($name, $parent);

		$this->dir_opendir = true;
	}

	protected function setAssertionManager(test\assertion\manager $assertionManager = null)
	{
		parent::setAssertionManager($assertionManager);

		$node = $this;

		$this->assertionManager
			->setHandler('directory', function($name = null) use ($node) { return $node->getNewDirectory($name); })
			->setHandler('file', function($name = null) use ($node) { return $node->getNewFile($name); })
		;

		return $this;
	}

	public function getNewDirectory($name = null)
	{
		$directory = new directory($name, $this);

		if (isset(stream::$streamsSize[(string) $this->getStream()]) === false)
		{
			stream::$streamsSize[(string) $this->getStream()] = 0;
		}

		$this->addChild($directory);

		return $directory;
	}

	public function getNewFile($name = null)
	{
		$file = new file($name, $this);

		if (isset(stream::$streamsSize[(string) $this->getStream()]) === false)
		{
			stream::$streamsSize[(string) $this->getStream()] = 0;
		}

		$this->addChild($file);

		return $file;
	}

	protected function addChild(node $child)
	{
		$this->getStream()->readdir[++stream::$streamsSize[(string) $this->getStream()]] = $child->getStream();

		return $this;
	}
}
