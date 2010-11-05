<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class errors extends report\fields\runner
{
	protected $runner = null;

	public function getRunner()
	{
		return $this->runner;
	}

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		if ($this->runner !== $runner)
		{
			$this->runner = $runner;
		}

		return $this;
	}

	public function toString()
	{
		$string = '';

		if ($this->runner !== null)
		{
			$errors = $this->runner->getScore()->getErrors();

			$sizeOfErrors = sizeof($errors);

			if ($sizeOfErrors > 0)
			{
				$string .= sprintf($this->locale->__('There is %d error:', 'There are %d errors:', $sizeOfErrors), $sizeOfErrors) . PHP_EOL;

				$class = null;
				$method = null;

				foreach ($errors as $error)
				{
					if ($error['class'] !== $class || $error['method'] !== $method)
					{
						$string .= '  ' . $error['class'] . '::' . $error['method'] . '():' . PHP_EOL;

						$class = $error['class'];
						$method = $error['method'];
					}

					$string .= '    ' . sprintf($this->locale->_('Error %s in file %s on line %d:'), $error['type'], $error['file'], $error['line']) . PHP_EOL;

					foreach (explode(PHP_EOL, rtrim($error['message'])) as $line)
					{
						$string .= '      ' . $line . PHP_EOL;
					}
				}
			}
		}

		return $string;
	}
}

?>
