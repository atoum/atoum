<?php
namespace mageekguy\atoum\filesystem;

use
	mageekguy\atoum\test,
	mageekguy\atoum\mock\stream
;

class file extends node
{
	public function __construct($name = null, node $parent = null)
	{
		parent::__construct($name, $parent);

		$this->setContent('');
	}

	protected function setAssertionManager(test\assertion\manager $assertionManager = null)
	{
		parent::setAssertionManager($assertionManager);

		$node = $this;

		$this->assertionManager
			->setHandler('content', function($content) use ($node) { return $node->setContent($content); })
		;

		return $this;
	}

	public function setContent($content)
	{
		$this->file_get_contents = $content;

		return $this;
	}
}
