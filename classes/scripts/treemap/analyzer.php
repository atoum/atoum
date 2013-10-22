<?php

namespace atoum\scripts\treemap;

interface analyzer
{
	public function getMetricName();
	public function getMetricLabel();
	public function getMetricFromFile(\splFileInfo $file);
}
