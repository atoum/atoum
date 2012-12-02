<?php
namespace mageekguy\atoum\filesystem;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\test\assertion,
	mageekguy\atoum\mock\stream
;

abstract class node
{
	private $name;
	private $parent;
	private $stream;
	protected $assertionManager;

	public function __construct($name = null, node $parent = null)
	{
		$this->name = $name ?: uniqid();
		$this->parent = $parent;

		$this
			->setStream($this->name, $parent)
			->setAssertionManager()
		;
	}

	protected function setAssertionManager(test\assertion\manager $assertionManager = null)
	{
		$this->assertionManager = $assertionManager ?: new test\assertion\manager();

		$node = $this;

		$returnParentHandler = function() use ($node) { return $node->getParent(); };

		$this->assertionManager
			->setHandler('parent', $returnParentHandler)
			->setHandler('end', $returnParentHandler)
			->setHandler('close', $returnParentHandler)
		;

		$returnNameHandler = function() use ($node) { return $node->getName(); };

		$this->assertionManager
			->setHandler('name', $returnNameHandler)
			->setHandler('basename', $returnNameHandler)
		;

		return $this;
	}

	protected function setStream($name, node $parent = null)
	{
		if($parent !== null)
		{
			$this->stream = stream::getSubStream($parent->getStream(), $name);
		}
		else
		{
			$this->stream = stream::get($name);
		}

		return $this;
	}

	public function getStream()
	{
		return $this->stream;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function getName()
	{
		return $this->name;
	}

	public function referencedBy(& $reference)
	{
		return $reference = $this;
	}

	public function __call($method, array $arguments = array())
	{
		try
		{
			return $this->assertionManager->invoke($method, $arguments);
		}
		catch(assertion\manager\exception $exception)
		{
			return $this->getStream()->invoke($method, $arguments);
		}
	}

	public function __get($property)
	{
		try
		{
			return $this->assertionManager->invoke($property);
		}
		catch(assertion\manager\exception $exception)
		{
			return $this->getStream()->__get($property);
		}
	}

	public function __set($method, $value)
	{
		return $this->getStream()->__set($method, $value);
	}

	public function __isset($method)
	{
		return$this->getStream()->__isset($method);
	}

	public function __toString()
	{
		return (string) $this->stream;
	}
}
