<?php

namespace mageekguy\atoum\template;

use
	mageekguy\atoum
;

class data
{
	protected $parent = null;
	protected $rank = null;
	protected $data = null;

	public function __construct($data = null)
	{
		$this->setData($data);
	}

	public function __toString()
	{
		return $this->getData();
	}

	public function resetData()
	{
		$this->data = null;

		return $this;
	}

	public function getData()
	{
		return ($this->data === null ? '' : $this->data);
	}

	public function setData($data)
	{
		return $this->resetData()->addData($data);
	}

	public function addData($data)
	{
		$data = (string) $data;

		if ($data != '')
		{
			$this->data .= $data;
		}

		return $this;
	}

	public function setParent(atoum\template $parent)
	{
		$parent->addChild($this);

		return $this;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function getRoot()
	{
		$root = $this;

		while ($root->parent !== null)
		{
			$root = $root->parent;
		}

		return $root;
	}

	public function isRoot()
	{
		return ($this->parent === null);
	}

	public function parentIsSet()
	{
		return ($this->parent !== null);
	}

	public function unsetParent()
	{
		if ($this->parentIsSet() === true)
		{
			$this->parent->deleteChild($this);
		}

		return $this;
	}

	public function build()
	{
		return $this;
	}

	public function addToParent()
	{
		if ($this->build()->parentIsSet() === true)
		{
			$this->parent->addData($this);
		}

		return $this;
	}

	public function getTag()
	{
		return null;
	}

	public function getId()
	{
		return null;
	}

	public function getByTag($tag)
	{
		return array();
	}

	public function getById($id, $fromRoot = true)
	{
		return null;
	}

	public function hasChildren()
	{
		return false;
	}

	public function getChild($rank)
	{
		return null;
	}

	public function getChildren()
	{
		return array();
	}
}
