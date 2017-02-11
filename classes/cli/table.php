<?php

namespace mageekguy\atoum\cli;

use mageekguy\atoum\exceptions\runtime;

class table
{
	private $rows = array();
	private $rowMaxLength = 0;
	private $columnsWidth = array();
	private $header;

	public function __construct(array $header = null)
	{
		$this->setHeader($header);
	}

	public function __toString()
	{
		$columnsWidth = $this->columnsWidth;
		$table = '';

		if ($this->header !== null)
		{
			$header = array_pad($this->header, $this->rowMaxLength, '');

			$table .= '| ' . implode(' | ',
				array_map(
					function($index, $column) use ($columnsWidth) {
						return str_pad($column, $columnsWidth[$index]);
					},
					array_keys($header),
					array_values($header)
				)
			) . ' |' . PHP_EOL;

			for ($i = 0; $i < $this->rowMaxLength; $i++)
			{
				$table .= '|' . str_repeat('-', $this->columnsWidth[$i] + 2);
			}

			$table .= '|' . PHP_EOL;
		}

		foreach ($this->rows as $row)
		{
			$row = array_pad($row, $this->rowMaxLength, '');

			$table .= '| ' . implode(' | ',
				array_map(
					function($index, $column) use ($columnsWidth) {
						return str_pad($column, $columnsWidth[$index]);
					},
					array_keys($row),
					array_values($row)
				)
			) . ' |' . PHP_EOL;
		}

		$this->reset();

		return $table . PHP_EOL;
	}

	public function setHeader(array $header = null)
	{
		if ($header === null)
		{
			return $this;
		}

		$this->header = array_values($header);

		return $this->computeRowMaxLength($this->header)->computeColumnWidths($this->header);
	}

	public function addRow(array $row)
	{
		$row = array_values($row);

		for ($index = 1, $size = sizeof($row); $index <= $size; $index++)
		{
			if (is_array($row[$index - 1]) === true)
			{
				if ($index < $size)
				{
					throw new runtime('Only the last column can be repeated');
				}

				if (sizeof($row[$index - 1]) === 1)
				{
					$row[$index - 1] = array_pop($row[$index - 1]);
				}
			}
		}

		if (is_array($row[sizeof($row) - 1]) === false)
		{
			$this->computeColumnWidths($this->rows[] = $row);
		}
		else
		{
			$lastColumn = array_pop($row);
			$this->computeColumnWidths($this->rows[] = array_merge($row, array(array_shift($lastColumn))));

			foreach ($lastColumn as $column)
			{
				$this->computeColumnWidths($this->rows[] = array_merge(array_fill(0, sizeof($row), ''), array($column)));
			}
		}

		return $this->computeRowMaxLength($row);
	}

	public function reset()
	{
		$this->rows = array();
		$this->rowMaxLength = 0;
		$this->columnsWidth = array();

		if ($this->header !== null)
		{
			$this->computeRowMaxLength($this->header)->computeColumnWidths($this->header);
		}

		return $this;
	}

	private function computeRowMaxLength(array $row)
	{
		$rowLength = sizeof($row);

		if ($rowLength > $this->rowMaxLength)
		{
			$this->rowMaxLength = $rowLength;
		}

		return $this;
	}

	private function computeColumnWidths(array $row)
	{
		foreach ($row as $index => $column)
		{
			$columnWidth = strlen($column);

			if (isset($this->columnsWidth[$index]) === false || $columnWidth > $this->columnsWidth[$index])
			{
				$this->columnsWidth[$index] = $columnWidth;
			}
		}

		return $this;
	}
}
