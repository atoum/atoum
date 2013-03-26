<?php

namespace mageekguy\atoum\scripts\treemap\analyzer;

use
	mageekguy\atoum\scripts\treemap\analyzer
;

class generic implements analyzer
{
	protected $metricName = '';
	protected $metricLabel = '';
	protected $callback = null;

	public function __construct($metricName, $metricLabel = null, $callback = null)
	{
		$this
			->setCallback($callback)
			->setMetricName($metricName)
			->setMetricLabel($metricLabel)
		;
	}

	public function setCallback(\closure $callback = null)
	{
		$this->callback = $callback ?: function() { return 0; };

		return $this;
	}

	public function getCallback()
	{
		return $this->callback;
	}

	public function setMetricName($metricName)
	{
		$this->metricName = (string) $metricName;

		return $this->setMetricLabel(ucfirst($this->metricName));
	}

	public function getMetricName()
	{
		return $this->metricName;
	}

	public function setMetricLabel($metricLabel = null)
	{
		$this->metricLabel = ($metricLabel ? (string) $metricLabel : ucfirst($this->metricName));

		return $this;
	}

	public function getMetricLabel()
	{
		return $this->metricLabel;
	}

	public function getMetricFromFile(\splFileInfo $file)
	{
		return call_user_func_array($this->callback, array($file));
	}
}
