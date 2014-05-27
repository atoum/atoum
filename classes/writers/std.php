<?php

namespace mageekguy\atoum\writers;

use
	mageekguy\atoum,
	mageekguy\atoum\reports,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\report\writers
;

abstract class std extends atoum\writer implements writers\realtime, writers\asynchronous
{
	protected $cli = null;
	protected $resource = null;

	public function __construct(atoum\cli $cli = null, atoum\adapter $adapter = null)
	{
		parent::__construct($adapter);

		$this->setCli($cli);
	}

	public function __destruct()
	{
		if ($this->resource !== null)
		{
			$this->adapter->fclose($this->resource);
		}
	}

	public function setCli(atoum\cli $cli = null)
	{
		$this->cli = $cli ?: new atoum\cli();

		return $this;
	}

	public function getCli()
	{
		return $this->cli;
	}

	public function clear()
	{
		return $this->doWrite($this->cli->isTerminal() === false ? PHP_EOL : "\033[1K\r");
	}

	public function writeRealtimeReport(reports\realtime $report, $event)
	{
		return $this->write((string) $report);
	}

	public function writeAsynchronousReport(reports\asynchronous $report)
	{
		return $this->write((string) $report);
	}

	protected function doWrite($something)
	{
		$this->init()->adapter->fwrite($this->resource, $something);

		return $this;
	}

	protected abstract function init();
}
