<?php

namespace mageekguy\atoum\scripts\loop;

use mageekguy\atoum\php;
use mageekguy\atoum\scripts\loop\strategy;

class runner
{
	protected $strategy;

	public function __construct()
	{
		$this
			->setStrategy()
		;
	}

	public function setStrategy(strategy $strategy = null)
	{
		$this->strategy = $strategy ?: new strategy\prompt();

		return $this;
	}

	public function getStrategy()
	{
		return $this->strategy;
	}

	public function run(\mageekguy\atoum\scripts\runner $runner)
	{
		$php = new php();
		$php
			->addOption('-f', $_SERVER['argv'][0])
			->addArgument('--disable-loop-mode');

		if ($runner->getCli()->isTerminal() === true) {
			$php->addArgument('--force-terminal');
		}

		$addScoreFile = false;

		foreach ($runner->getArgumentsParser()->getValues() as $argument => $values) {
			switch ($argument) {
				case '-l':
				case '--loop':
				case '--disable-loop-mode':
					break;

				case '-sf':
				case '--score-file':
					$addScoreFile = true;
					break;

				default:
					if ($runner->getArgumentsParser()->argumentHasHandler($argument) === false) {
						$php->addArgument('-f', $argument);
					} else {
						$php->addArgument($argument, join(' ', $values));
					}
			}
		}

		if ($runner->getScoreFile() === null) {
			$runner->setScoreFile(sys_get_temp_dir() . '/atoum.score');

			@unlink($runner->getScoreFile());

			$addScoreFile = true;
		}

		if ($addScoreFile === true) {
			$php->addArgument('--score-file', $runner->getScoreFile());
		}

		while ($runner->canRun() === true) {
			passthru((string)$php);

			if (!$runner->isLoopModeEnabled() || $this->getStrategy()->runAgain($runner) === false) {
				$runner->stopRun();
			}
		}
	}
}
