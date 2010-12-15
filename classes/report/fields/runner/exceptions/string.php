<?php

namespace mageekguy\atoum\report\fields\runner\exceptions;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\exceptions
{
	const titlePrompt = '> ';
	const methodPrompt = '=> ';
	const exceptionPrompt = '==> ';

	public function __toString()
	{
		$string = '';

		if ($this->runner !== null)
		{
			$exceptions = $this->runner->getScore()->getExceptions();

			$sizeOfErrors = sizeof($exceptions);

			if ($sizeOfErrors > 0)
			{
				$string .= self::titlePrompt . sprintf($this->locale->__('There is %d exception:', 'There are %d exceptions:', $sizeOfErrors), $sizeOfErrors) . PHP_EOL;

				$class = null;
				$method = null;

				foreach ($exceptions as $exception)
				{
					if ($exception['class'] !== $class || $exception['method'] !== $method)
					{
						$string .= self::methodPrompt . $exception['class'] . '::' . $exception['method'] . '():' . PHP_EOL;

						$class = $exception['class'];
						$method = $exception['method'];
					}

					$string .= self::exceptionPrompt . sprintf($this->locale->_('Exception throwed in file %s on line %d:'), $exception['file'], $exception['line']) . PHP_EOL;

					foreach (explode(PHP_EOL, rtrim($exception['value'])) as $line)
					{
						$string .= $line . PHP_EOL;
					}
				}
			}
		}

		return $string;
	}
}

?>
