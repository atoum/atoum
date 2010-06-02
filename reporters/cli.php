<?php

namespace mageekguy\tests\unit\reporters;

use \mageekguy\tests\unit\test;

class cli extends \mageekguy\tests\unit\reporter
{
	protected $run = 0;
	protected $start = 0.0;
	protected $progressBar = '';
	protected $testMethods = 0;
	protected $size = 0;

	public function manageObservableEvent(\mageekguy\tests\unit\observable $test, $event)
	{
		switch ($event)
		{
			case test::eventRunStart:
				$this->runStart($test);
				break;

			case test::eventBeforeTestMethod:
				$this->testMethods++;
				break;

			case test::eventSuccess:
				$this->progressBar('.');
				break;

			case test::eventFailure:
				$this->progressBar('F');
				break;

			case test::eventError:
				$this->progressBar('!');
				break;

			case test::eventException:
				$this->progressBar('E');
				break;

			case test::eventRunEnd:
				$this->runEnd($test);
				break;
		}
	}

	protected function runStart(\mageekguy\tests\unit\test $test)
	{
		if ($this->run == 0)
		{
			self::write(sprintf($this->locale->_('\mageekguy\tests\unit\test version %s by %s.'), $test->getVersion(), \mageekguy\tests\unit\test::author));

			$this->start = microtime(true);
			$this->progressBar = '';
			$this->testMethods = 0;
			$this->size = sizeof($test);
		}

		$this->progressBar();

		$this->run++;
	}

	protected function progressBar($dot = '')
	{
		$end = '[' . sprintf('%' . strlen($this->size) . 'd', $this->testMethods) . '/' . $this->size . ']';

		if (strlen($this->progressBar) >= 60)
		{
			self::write();
		}
		else
		{
			echo str_repeat("\010", 60 + strlen($end));
		}

		$this->progressBar .= $dot;

		echo str_pad(str_pad($this->progressBar, $this->size, '-', STR_PAD_RIGHT), 60, '_', STR_PAD_RIGHT) . $end;
	}

	protected function runEnd(\mageekguy\tests\unit\test $test)
	{
		$duration = (microtime(true) - $this->start);

		self::write();
		self::write(sprintf($this->locale->__('Duration: %4.2f second, Memory %4.2f Mb.', 'Duration: %4.2f seconds, Memory %4.2f Mb', $duration), $duration, memory_get_peak_usage(true) / 1048576));

		/*
		$score = $test->getScore();

		$failNumber = $score->getFailNumber();

		if ($failNumber > 0)
		{
			self::write();

			if ($failNumber > 1)
			{
				self::write('There were ' . $failNumber . ' failures:');
			}
			else
			{
				self::write('There was ' . $failNumber . ' failure:');
			}

			self::write();

			foreach ($score->getAssertions() as $class => $methods)
			{
				foreach ($methods as $method => $assertions)
				{
					$fails = array();

					foreach ($assertions as $assertion)
					{
						if ($assertion['fail'] !== null)
						{
							$fails[] = $assertion;
						}
					}

					if (sizeof($fails) > 0)
					{
						self::write($class . '::' . $method . '():', 1);

						foreach ($fails as $fail)
						{
							self::write($fail['asserter'] . ' failed because ' . $fail['fail'] . ' in file ' . $fail['file'] . ' at line ' . $fail['line'], 2);
						}

						self::write();
					}
				}
			}
		}

		$errorNumber = $score->getErrorNumber();

		if ($errorNumber > 0)
		{
			if ($errorNumber > 1)
			{
				self::write('There were ' . $errorNumber . ' errors:');
			}
			else
			{
				self::write('There was ' . $errorNumber . ' error:');
			}

			foreach ($score->getErrors() as $class => $methods)
			{
				foreach ($methods as $method => $errors)
				{
					self::write($class . '::' . $method . '():', 1);

					foreach ($errors as $error)
					{
						self::write('[' . $error['type'] . '] ' . $error['message'], 2);
					}
				}
			}
		}
		*/
	}

	public static function write($message = '', $level = 0)
	{
		if ($message != '')
		{
			echo ($level <= 0 ? '' : str_repeat("\t", $level)) . ltrim($message);
		}

		echo PHP_EOL;
	}
}

?>
