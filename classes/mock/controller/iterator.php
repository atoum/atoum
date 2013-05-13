<?php

namespace mageekguy\atoum\mock\controller;

use
	mageekguy\atoum\mock
;

class iterator implements \iteratorAggregate
{
	protected $controller = null;
	protected $filters = array();

	public function __construct(mock\controller $controller = null)
	{
		if ($controller != null)
		{
			$this->setMockController($controller);
		}
	}

	public function __set($keyword, $mixed)
	{
		foreach ($this->getMethods() as $method)
		{
			$this->controller->{$method}->{$keyword} = $mixed;
		}

		return $this;
	}

	public function getIterator()
	{
		return new \arrayIterator($this->getMethods());
	}

	public function setMockController(mock\controller $controller)
	{
		$this->controller = $controller;

		return $this;
	}

	public function getMockController()
	{
		return $this->controller;
	}

	public function getMethods()
	{
		$methods = ($this->controller === null ? array() : $this->controller->getMethods());

		foreach ($this->filters as $filter)
		{
			$methods = array_filter($methods, $filter);
		}

		return array_values(array_filter($methods, function($name) { return ($name !== '__construct'); }));
	}

	public function addFilter(\closure $filter)
	{
		$this->filters[] = $filter;

		return $this;
	}

	public function getFilters()
	{
		return $this->filters;
	}

	public function resetFilters()
	{
		$this->filters = array();

		return $this;
	}
}
