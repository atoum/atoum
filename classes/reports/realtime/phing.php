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
	protected $codeCoverageReportProjectName = null;

	public function __construct()
	{
		parent::__construct();

		$this->build();
	}

	public function showProgress()
	{
		$this->showProgress = true;

		return $this->build();
	}

	public function hideProgress()
	{
		$this->showProgress = false;

		return $this->build();
	}

	public function progressIsShowed()
	{
		return $this->showProgress;
	}

	public function showCodeCoverage()
	{
		$this->showCodeCoverage = true;

		return $this->build();
	}

	public function hideCodeCoverage()
	{
		$this->showCodeCoverage = false;

		return $this->build();
	}

	public function codeCoverageIsShowed()
	{
		return $this->showCodeCoverage;
	}

	public function showMissingCodeCoverage()
	{
		$this->showMissingCodeCoverage = true;

		return $this->build();
	}

	public function hideMissingCodeCoverage()
	{
		$this->showMissingCodeCoverage = false;

		return $this->build();
	}

	public function missingCodeCoverageIsShowed()
	{
		return $this->showMissingCodeCoverage;
	}

	public function showDuration()
	{
		$this->showDuration = true;

		return $this->build();
	}

	public function hideDuration()
	{
		$this->showDuration = false;

		return $this->build();
	}

	public function durationIsShowed()
	{
		return $this->showDuration;
	}

	public function showMemory()
	{
		$this->showMemory = true;

		return $this->build();
	}

	public function hideMemory()
	{
		$this->showMemory = false;

		return $this->build();
	}

	public function memoryIsShowed()
	{
		return $this->showMemory;
	}

	public function setCodeCoverageReportPath($path = null)
	{
		$this->codeCoverageReportPath = $path;

		return $this;
	}

	public function getCodeCoverageReportPath()
	{
		return $this->codeCoverageReportPath;
	}

	public function setCodeCoverageReportProjectName($url = null)
	{
		$this->codeCoverageReportProjectName = $url;

		return $this;
	}

	public function getCodeCoverageReportProjectName()
	{
		return $this->codeCoverageReportProjectName;
	}

	public function setCodeCoverageReportUrl($url = null)
	{
		$this->codeCoverageReportUrl = $url;

		return $this;
	}

	public function getCodeCoverageReportUrl()
	{
		return $this->codeCoverageReportUrl;
	}

	protected function build()
	{
		$this->resetFields();

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
		
		$uncompletedTestColorizer = new colorizer('0;37');
		$uncompletedTestMethodPrompt = clone $secondLevelPrompt;
		$uncompletedTestMethodPrompt->setColorizer($uncompletedTestColorizer);
		$uncompletedTestOutputPrompt = new prompt('  ', $uncompletedTestColorizer);
		
		$voidTestColorizer = new colorizer('0;34');
		$voidTestMethodPrompt = clone $secondLevelPrompt;
		$voidTestMethodPrompt->setColorizer($voidTestColorizer);

		$skippedTestColorizer = new colorizer('0;90');
		$skippedTestMethodPrompt = clone $secondLevelPrompt;
		$skippedTestMethodPrompt->setColorizer($skippedTestColorizer);


		$phpPathField = new runner\php\path\cli();
		$phpPathField
			->setPrompt($firstLevelPrompt)
			->setTitleColorizer($firstLevelColorizer)
		;

		$this->addField($phpPathField);

		$phpVersionField = new runner\php\version\cli();
		$phpVersionField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($firstLevelColorizer)
			->setVersionPrompt($secondLevelPrompt)
		;

		$this->addField($phpVersionField);

		if ($this->showDuration === true)
		{
			$runnerDurationField = new runner\duration\cli();
			$runnerDurationField
				->setPrompt($firstLevelPrompt)
				->setTitleColorizer($firstLevelColorizer)
			;

			$this->addField($runnerDurationField);
		}

		if ($this->showMemory === true)
		{
			$runnerTestsMemoryField = new runner\tests\memory\phing();
			$runnerTestsMemoryField
				->setPrompt($firstLevelPrompt)
				->setTitleColorizer($firstLevelColorizer)
			;

			$this->addField($runnerTestsMemoryField);
		}

		if ($this->showCodeCoverage === true)
		{
			$runnerTestsCoverageField = new runner\tests\coverage\phing();
			$runnerTestsCoverageField
				->setTitlePrompt($firstLevelPrompt)
				->setClassPrompt($secondLevelPrompt)
				->setMethodPrompt(new prompt('  ', $firstLevelColorizer))
				->setTitleColorizer($firstLevelColorizer)
			;

			if ($this->showMissingCodeCoverage === false)
			{
				$runnerTestsCoverageField->hideMissingCodeCoverage();
			}

			$this->addField($runnerTestsCoverageField);
		}

		$resultField = new runner\result\cli();
		$resultField
			->setPrompt($firstLevelPrompt)
			->setSuccessColorizer(new colorizer('0;37', '42'))
			->setFailureColorizer(new colorizer('0;37', '41'))
		;

		$this->addField($resultField);

		$failuresField = new runner\failures\cli();
		$failuresField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($failureColorizer)
			->setMethodPrompt($failurePrompt)
		;

		$this->addField($failuresField);

		$outputsField = new runner\outputs\cli();
		$outputsField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($firstLevelColorizer)
			->setMethodPrompt($secondLevelPrompt)
		;

		$this->addField($outputsField);

		$errorsField = new runner\errors\cli();
		$errorsField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($errorColorizer)
			->setMethodPrompt($errorPrompt)
		;

		$this->addField($errorsField);

		$exceptionsField = new runner\exceptions\cli();
		$exceptionsField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($exceptionColorizer)
			->setMethodPrompt($exceptionPrompt)
		;

		$this->addField($exceptionsField);

		$runnerUncompletedField = new runner\tests\uncompleted\cli();
		$runnerUncompletedField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($uncompletedTestColorizer)
			->setMethodPrompt($uncompletedTestMethodPrompt)
			->setOutputPrompt($uncompletedTestOutputPrompt)
		;

		$this->addField($runnerUncompletedField);

		$runnerVoidField = new runner\tests\void\cli();
		$runnerVoidField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($voidTestColorizer)
			->setMethodPrompt($voidTestMethodPrompt)
		;

		$this->addField($runnerVoidField);
		
		$runnerSkippedField = new runner\tests\skipped\cli();
		$runnerSkippedField
			->setTitlePrompt($firstLevelPrompt)
			->setTitleColorizer($skippedTestColorizer)
			->setMethodPrompt($skippedTestMethodPrompt)
		;
		
		$this->addField($runnerSkippedField);

		if ($this->showProgress === true)
		{
			$runField = new test\run\phing();
			$runField
				->setPrompt($firstLevelPrompt)
				->setColorizer($firstLevelColorizer)
			;

			$this
				->addField($runField)
				->addField(new test\event\phing())
			;
		}

		if ($this->showDuration === true)
		{
			$durationField = new test\duration\phing();
			$durationField
				->setPrompt($secondLevelPrompt)
			;

			$this->addField($durationField);
		}

		if ($this->showMemory === true)
		{
			$memoryField = new test\memory\phing();
			$memoryField
				->setPrompt($secondLevelPrompt)
			;

			$this->addField($memoryField);
		}

		if ($this->codeCoverageReportPath !== null)
		{
			$coverageField = new atoum\report\fields\runner\coverage\html($this->codeCoverageReportProjectName ?: '', $this->codeCoverageReportPath);
			$coverageField->setRootUrl($this->codeCoverageReportUrl ?: 'file:////' . realpath($this->codeCoverageReportPath));

			$this->addField($coverageField);
		}

		return $this;
	}
}
