<?php

namespace mageekguy\atoum\scripts\treemap;

interface analyzer
{
	public function getMetricName();
	public function getMetricFromFile(\splFileInfo $file);
}
