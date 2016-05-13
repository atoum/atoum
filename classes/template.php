<?php

namespace mageekguy\atoum;

class template extends template\data
{
	protected $children = array();

	public function __set($tag, $data)
	{
		foreach ($this->getByTag($tag) as $child)
		{
			$child->setData($data);
		}

		return $this;
	}

	public function __get($tag)
	{
		return $this->getByTag($tag);
	}

	public function __unset($tag)
	{
		foreach ($this->getByTag($tag) as $child)
		{
			$child->resetData();
		}

		return $this;
	}

	public function __isset($tag)
	{
		return (sizeof($this->getByTag($tag)) > 0);
	}

	public function getByTag($tag)
	{
		$iterator = new template\iterator();

		return $iterator->addTag($tag, $this);
	}

	public function getById($id, $fromRoot = true)
	{
		$root = $fromRoot === false ? $this : $this->getRoot();

		if ($root->getId() === $id)
		{
			return $root;
		}
		else
		{
			foreach ($root->children as $child)
			{
				$tag = $child->getById($id, false);

				if ($tag !== null)
				{
					return $tag;
				}
			}

			return null;
		}
	}

	public function getChild($rank)
	{
		return (isset($this->children[$rank]) === false ? null : $this->children[$rank]);
	}

	public function getChildren()
	{
		return array_values($this->children);
	}

	public function setWith($mixed)
	{
		foreach ($mixed as $tag => $value)
		{
			$this->{$tag} = $value;
		}

		return $this;
	}

	public function resetChildrenData()
	{
		foreach ($this->children as $child)
		{
			$child->resetData();
		}

		return $this;
	}

	public function build($mixed = array())
	{
		foreach ($this->setWith($mixed)->children as $child)
		{
			$this->addData($child->getData());
		}

		return parent::build();
	}

	public function hasChildren()
	{
		return (sizeof($this->children) > 0);
	}

	public function isChild(template\data $child)
	{
		return ($child->parent === $this);
	}

	public function addToParent($mixed = array())
	{
		$this->setWith($mixed);

		return parent::addToParent();
	}

	public function addChild(template\data $child)
	{
		if ($this->isChild($child) === false)
		{
			$id = $child->getId();

			if ($id !== null && $this->idExists($id) === true)
			{
				throw new exceptions\runtime('Id \'' . $id . '\' is already defined');
			}

			if ($child->parentIsSet() === true)
			{
				$child->unsetParent();
			}

			$child->rank = sizeof($this->children);
			$this->children[$child->rank] = $child;
			$child->parent = $this;
		}

		return $this;
	}

	public function deleteChild(template\data $child)
	{
		if ($this->isChild($child) === true)
		{
			unset($this->children[$child->rank]);
			$child->parent = null;
			$child->rank = null;
		}

		return $this;
	}

	public function idExists($id)
	{
		return ($this->getById($id) !== null);
	}

	public function setAttribute($name, $value) {}

	public function unsetAttribute($name) {}
}
