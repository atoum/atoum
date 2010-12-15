<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class errors extends report\fields\runner
{
	const titlePrompt = '> ';
	const methodPrompt = '=> ';
	const errorPrompt = '==> ';

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

	public function __toString()
	{
		$string = '';

		if ($this->runner !== null)
		{
			$errors = $this->runner->getScore()->getErrors();

			$sizeOfErrors = sizeof($errors);

			if ($sizeOfErrors > 0)
			{
				$string .= self::titlePrompt . sprintf($this->locale->__('There is %d error:', 'There are %d errors:', $sizeOfErrors), $sizeOfErrors) . PHP_EOL;

				$class = null;
				$method = null;

				foreach ($errors as $error)
				{
					if ($error['class'] !== $class || $error['method'] !== $method)
					{
						$string .= self::methodPrompt . $error['class'] . '::' . $error['method'] . '():' . PHP_EOL;

						$class = $error['class'];
						$method = $error['method'];
					}

					$string .= self::errorPrompt;

					$type = self::getType($error['type']);

					switch (true)
					{
						case $error['file'] === null && $error['line'] === null:
							$string .= sprintf($this->locale->_('Error %s in unknown file on unknown line:'), $type);
							break;

						case $error['file'] === null && $error['line'] !== null:
							$string .= sprintf($this->locale->_('Error %s in unknown file on line %d:'), $type, $error['line']);
							break;

						case $error['file'] !== null && $error['line'] === null:
							$string .= sprintf($this->locale->_('Error %s in %s on unknown line:'), $type, $error['file']);
							break;

						case $error['file'] !== null && $error['line'] !== null:
							$string .= sprintf($this->locale->_('Error %s in %s on line %d:'), $type, $error['file'], $error['line']);
							break;
					}

					$string .= PHP_EOL;

					foreach (explode(PHP_EOL, rtrim($error['message'])) as $line)
					{
						$string .= $line . PHP_EOL;
					}
				}
			}
		}

		return $string;
	}

	public static function getType($error)
	{
		switch ($error)
		{
			case E_ERROR:
				return 'E_ERROR';

			case E_WARNING:
				return 'E_WARNING';

			case E_NOTICE:
				return 'E_NOTICE';

			case E_USER_NOTICE:
				return 'E_USER_NOTICE';

			case E_USER_WARNING:
				return 'E_USER_WARNING';

			case E_USER_ERROR:
				return 'E_USER_ERROR';

			case E_RECOVERABLE_ERROR:
				return 'E_RECOVERABLE_ERROR';

			case E_DEPRECATED:
				return 'E_DEPRECATED';

			case E_USER_DEPRECATED:
				return 'E_USER_DEPRECATED';

			default:
				return $error;
		}
	}
}

?>
