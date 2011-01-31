<?php

namespace mageekguy\atoum;

use \mageekguy\atoum\template;
use \mageekguy\atoum\exceptions;

class template extends template\data implements \iteratorAggregate
{
	protected $children = array();

	public function __construct($data = null)
	{
		parent::__construct($data);
	}

	public function __set($tag, $data)
	{
		$this->tagExists($tag, $tags);

		foreach ($tags as $child)
		{
			$child->setData($data);
		}
	}

	public function __get($tag)
	{
		return new template\iterator($this->getByTag($tag));
	}

	public function __unset($tag)
	{
		$this->tagExists($tag, $tags);

		foreach ($this->getByTag($tag) as $child)
		{
			$child->resetData();
		}
	}

	public function __isset($tag)
	{
		return (sizeof($this->getByTag($tag)) > 0);
	}

	public function getByTag($tag)
	{
		$tags = array();

		foreach ($this as $child)
		{
			if ($child->getTag() == $tag)
			{
				$tags[] = $child;
			}

			$tags = array_merge($tags, $child->getByTag($tag));
		}

		return $tags;
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
			foreach ($root->getChildren() as $child)
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
		if (is_int($rank) === false || $rank < 0)
		{
			trigger_error('Rank must be an integer greater than or equal to zero', E_USER_ERROR);
		}
		else
		{
			return (isset($this->children[$rank]) === false ? null : $this->children[$rank]);
		}
	}

	public function getChildren()
	{
		return array_values($this->children);
	}

	public function getIterator() {
		return new template\iterator(array_values($this->children));
	}

	public function setWith($mixed)
	{
		foreach ($mixed as $tag => $value)
		{
			$this->{$tag} = $value;
		}

		return $this;
	}

	public function build($mixed = array())
	{
		foreach ($this->setWith($mixed) as $child)
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

	public function checkChild(template\data $child)
	{
		if ($this->isChild($child) === false)
		{
			$id = $child->getId();

			if ($id !== null && $this->getById($id) !== null)
			{
				return false;
			}
			else
			{
				foreach ($child->getChildren() as $child)
				{
					if ($this->checkChild($child) === false)
					{
						return false;
					}
				}
			}
		}

		return true;
	}

	public function addToParent($mixed = array())
	{
		$this->setWith($mixed);

		return parent::addToParent();
	}

	public function addChild(template\data $child)
	{
		if ($this->checkChild($child) === false)
		{
			trigger_error('Some id are already defined', E_USER_ERROR);
		}
		else
		{
			if ($this->isChild($child) === false)
			{
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

	protected function tagExists($tag, & $tags)
	{
		$tags = $this->getByTag($tag);

		if (sizeof($tags) <= 0)
		{
			throw new exceptions\runtime('Tag \'' . $tag . '\' does not exist');
		}
	}
}

?>
