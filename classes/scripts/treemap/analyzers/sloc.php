<?php

namespace mageekguy\atoum\scripts\treemap\analyzers;

use
	mageekguy\atoum\scripts\treemap\analyzer
;

class sloc implements analyzer
{
	public function getMetricName()
	{
		return 'sloc';
	}

	public function getMetricLabel()
	{
		return 'Source Line Of Code';
	}

	public function getMetricFromFile(\splFileInfo $file)
	{
		$codeLines = 0;
		$blankLines = 0;

		foreach ($file->openFile() as $line)
		{
			if (preg_match('/^\s+$/', $line))
			{
				$blankLines++;
			}
			else
			{
				$codeLines++;
			}
		}

		$totalLines = $codeLines + $blankLines;

		return $totalLines === 0 ? 0 : ($blankLines / $totalLines <= 0.25 ? $totalLines : $codeLines);
	}
}
