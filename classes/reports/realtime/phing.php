<?php

namespace mageekguy\atoum\reports\realtime;

use
	mageekguy\atoum,
	mageekguy\atoum\cli\prompt,
	mageekguy\atoum\cli\colorizer,
	mageekguy\atoum\reports\realtime,
	mageekguy\atoum\report\fields\test,
	mageekguy\atoum\report\fields\runner
;

class phing extends realtime
{
	protected $showProgress = true;
	protected $showMissingCodeCoverage = true;
	protected $showDuration = true;
	protected $showMemory = true;
	protected $showCodeCoverage = true;
	protected $codeCoverageReportPath = null;
	protected $codeCoverageReportUrl = null;

	public function __construct($showProgress = true, $showCodeCoverage = true, $showMissingCodeCoverage = true, $showDuration = true, $showMemory = true, $codeCoverageReportPath = null, $codeCoverageReportUrl = null, atoum\factory $factory = null)
	{
		parent::__construct($factory);

		$this->showProgress = ($showProgress == true);
		$this->showCodeCoverage = ($showCodeCoverage == true);
		$this->showMissingCodeCoverage = ($showMissingCodeCoverage == true);
		$this->showDuration = ($showDuration == true);
		$this->showMemory = ($showMemory == true);
		$this->codeCoverageReportPath = ($codeCoverageReportPath === null ? null : (string) $codeCoverageReportPath);
		$this->codeCoverageReportUrl = ($codeCoverageReportUrl === null ? null : (string) $codeCoverageReportUrl);

		$firstLevelPrompt = new prompt(PHP_EOL);
		$firstLevelColorizer = new colorizer('1;36');

		$secondLevelPrompt = new prompt(' ', $firstLevelColorizer);

		$failureColorizer = new colorizer('0;31');
		$failurePrompt = clone $secondLevelPrompt;
		$failurePrompt->setColorizer($failureColorizer);

		$errorColorizer = new colorizer('0;33');
		$errorPrompt = clone $secondLevelPrompt;
		$errorPrompt->setColorizer($errorColorizer);

		$exceptionColorizer = new colorizer('0;35');
		$exceptionPrompt = clone $secondLevelPrompt;
		$exceptionPrompt->setColorizer($exceptionColorizer);

		$this
			 ->addField(new runner\atoum\phing(
					$firstLevelPrompt,
					$firstLevelColorizer
				)
			)
			->addField(new runner\php\path\cli(
					$firstLevelPrompt,
					$firstLevelColorizer
				)
			)
			->addField(new runner\php\version\cli(
					$firstLevelPrompt,
					$firstLevelColorizer,
					$secondLevelPrompt
				)
			)
		;

		if ($this->showDuration === true)
		{
			$this->addField(new runner\duration\cli(
					$firstLevelPrompt,
					$firstLevelColorizer
				)
			);
		}

		if ($this->showMemory === true)
		{
			$this->addField(new runner\tests\memory\phing(
					$firstLevelPrompt,
					$firstLevelColorizer
				)
			);
		}

		if ($this->showCodeCoverage === true)
		{
			$this->addField(new runner\tests\coverage\cli(
					$firstLevelPrompt,
					$secondLevelPrompt,
					new prompt('  ', $firstLevelColorizer),
					$firstLevelColorizer,
					null,
					null,
					$this->showMissingCodeCoverage
				)
			);
		}

		$this
			->addField(new runner\result\cli(
					$firstLevelPrompt,
					new colorizer('0;37', '42'),
					new colorizer('0;37', '41')
				)
			)
			->addField(new runner\failures\cli(
					$firstLevelPrompt,
					$failureColorizer,
					$failurePrompt
				)
			)
			->addField(new runner\outputs\cli(
					$firstLevelPrompt,
					$firstLevelColorizer,
					$secondLevelPrompt
				)
			)
			->addField(new runner\errors\cli(
					$firstLevelPrompt,
					$errorColorizer,
					$errorPrompt
				)
			)
			->addField(new runner\exceptions\cli(
					$firstLevelPrompt,
					$exceptionColorizer,
					$exceptionPrompt
				)
			)
		;

		if ($this->showProgress === true)
		{
			$this
				->addField(new test\run\phing(
						$firstLevelPrompt,
						$firstLevelColorizer
					)
				)
			  ->addField(new test\event\phing())
			;
		}

		if ($this->showDuration === true)
		{
			$this->addField(new test\duration\phing(
					$secondLevelPrompt
				)
			);
		}

		if ($this->showMemory === true)
		{
			$this->addField(new test\memory\phing(
					$secondLevelPrompt
				)
			);
		}

		if ($codeCoverageReportPath = $this->getCodecoverageReportPath())
		{
			$coverageField = new atoum\report\fields\runner\coverage\html('', $codeCoverageReportPath);

			if ($this->codeCoverageReportUrl === null)
			{
				$coverageField->setRootUrl("file:////" . realpath($this->getCodeCoverageReportPath()));
			}
			else
			{
				$coverageField->setRootUrl($this->getCodecoveragereporturl());
			}

			$this->addField($coverageField);
		}
	}

	public function codeCoverageIsShowed()
	{
		return ($this->showCodeCoverage === true);
	}

	public function durationIsShowed()
	{
		return ($this->showDuration === true);
	}

	public function memoryIsShowed()
	{
		return ($this->showMemory === true);
	}

	public function missingCodeCoverageIsShowed()
	{
		return ($this->showMissingCodeCoverage === true);
	}

	public function progressIsShowed()
	{
		return ($this->showProgress === true);
	}

	public function getCodeCoverageReportPath()
	{
		return $this->codeCoverageReportPath;
	}

	public function getCodecoveragereporturl()
	{
		return $this->codeCoverageReportUrl;
	}
}
