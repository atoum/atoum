<?php

namespace mageekguy\atoum\test;

use
	mageekguy\atoum
;

class score extends atoum\score
{
	private $case = null;
	private $dataSetKey = null;
	private $dataSetProvider = null;

	public function reset()
	{
		return parent::reset()
			->unsetCase()
			->unsetDataSet()
		;
	}

	public function addFail($file, $class, $method, $line, $asserter, $reason, $case = null, $dataSetKey = null, $dataSetProvider = null)
	{
		return parent::addFail($file, $class, $method, $line, $asserter, $reason, $case ?: $this->case, $dataSetKey ?: $this->dataSetKey, $dataSetProvider ?: $this->dataSetProvider);
	}

	public function addException($file, $class, $method, $line, \exception $exception, $case = null, $dataSetKey = null, $dataSetProvider = null)
	{
		return parent::addException($file, $class, $method, $line, $exception, $case ?: $this->case, $dataSetKey ?: $this->dataSetKey, $dataSetProvider ?: $this->dataSetProvider);
	}

	public function addError($file, $class, $method, $line, $type, $message, $errorFile = null, $errorLine = null, $case = null, $dataSetKey = null, $dataSetProvider = null)
	{
		return parent::addError($file, $class, $method, $line, $type, $message, $errorFile, $errorLine, $case ?: $this->case, $dataSetKey ?: $this->dataSetKey, $dataSetProvider ?: $this->dataSetProvider);
	}

	public function getCase()
	{
		return $this->case;
	}

	public function setCase($case)
	{
		$this->case = (string) $case;

		return $this;
	}

	public function unsetCase()
	{
		$this->case = null;

		return $this;
	}

	public function setDataSet($key, $dataProvider)
	{
		$this->dataSetKey = $key;
		$this->dataSetProvider = $dataProvider;

		return $this;
	}

	public function unsetDataSet()
	{
		$this->dataSetKey = null;
		$this->dataSetProvider = null;

		return $this;
	}

	public function getDataSetKey()
	{
		return $this->dataSetKey;
	}

	public function getDataSetProvider()
	{
		return $this->dataSetProvider;
	}
}
