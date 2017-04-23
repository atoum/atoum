<?php

namespace mageekguy\atoum\reports\realtime\cli;

use mageekguy\atoum\cli\colorizer;
use mageekguy\atoum\cli\prompt;
use mageekguy\atoum\report\field\decorators\travis\fold;
use mageekguy\atoum\report\fields\runner;
use mageekguy\atoum\report\fields\test;
use mageekguy\atoum\reports\realtime;

class travis extends realtime
{
    public function __construct()
    {
        parent::__construct();

        $defaultColorizer = new colorizer('1;36');

        $firstLevelPrompt = new prompt('> ');
        $secondLevelPrompt = new prompt('=> ', $defaultColorizer);
        $thirdLevelPrompt = new prompt('==> ', $defaultColorizer);

        $atoumPathField = new runner\atoum\path\cli();
        $atoumPathField
            ->setPrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
        ;

        $this->addField($atoumPathField);

        $atoumVersionField = new runner\atoum\version\cli();
        $atoumVersionField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
        ;

        $this->addField($atoumVersionField);

        $phpPathField = new runner\php\path\cli();
        $phpPathField
            ->setPrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
        ;

        $this->addField($phpPathField);

        $phpVersionField = new runner\php\version\cli();
        $phpVersionField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
            ->setVersionPrompt($secondLevelPrompt)
        ;

        $this->addField($phpVersionField);

        $runnerTestsDurationField = new runner\tests\duration\cli();
        $runnerTestsDurationField
            ->setPrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
        ;

        $this->addField($runnerTestsDurationField);

        $runnerTestsMemoryField = new runner\tests\memory\cli();
        $runnerTestsMemoryField
            ->setPrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
        ;

        $this->addField($runnerTestsMemoryField);

        $this->runnerTestsCoverageField = new runner\tests\coverage\cli();
        $this->runnerTestsCoverageField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
            ->setClassPrompt($secondLevelPrompt)
            ->setMethodPrompt(new prompt('==> ', $defaultColorizer))
        ;

        $this->addField(new fold($this->runnerTestsCoverageField, 'coverage'));

        $runnerDurationField = new runner\duration\cli();
        $runnerDurationField
            ->setPrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
        ;

        $this->addField($runnerDurationField);

        $runnerResultField = new runner\result\cli();
        $runnerResultField
            ->setSuccessColorizer(new colorizer('0;37', '42'))
            ->setFailureColorizer(new colorizer('0;37', '41'))
        ;

        $this->addField($runnerResultField);

        $failureColorizer = new colorizer('0;31');
        $failurePrompt = clone $secondLevelPrompt;
        $failurePrompt->setColorizer($failureColorizer);

        $runnerFailuresField = new runner\failures\cli();
        $runnerFailuresField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($failureColorizer)
            ->setMethodPrompt($failurePrompt)
        ;

        $this->addField(new fold($runnerFailuresField, 'failures'));

        $runnerOutputsField = new runner\outputs\cli();
        $runnerOutputsField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($defaultColorizer)
            ->setMethodPrompt($secondLevelPrompt)
        ;

        $this->addField(new fold($runnerOutputsField, 'outputs'));

        $errorColorizer = new colorizer('0;33');
        $errorMethodPrompt = clone $secondLevelPrompt;
        $errorMethodPrompt->setColorizer($errorColorizer);
        $errorPrompt = clone $thirdLevelPrompt;
        $errorPrompt->setColorizer($errorColorizer);

        $runnerErrorsField = new runner\errors\cli();
        $runnerErrorsField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($errorColorizer)
            ->setMethodPrompt($errorMethodPrompt)
            ->setErrorPrompt($errorPrompt)
        ;

        $this->addField(new fold($runnerErrorsField, 'errors'));

        $exceptionColorizer = new colorizer('0;35');
        $exceptionMethodPrompt = clone $secondLevelPrompt;
        $exceptionMethodPrompt->setColorizer($exceptionColorizer);
        $exceptionPrompt = clone $thirdLevelPrompt;
        $exceptionPrompt->setColorizer($exceptionColorizer);

        $runnerExceptionsField = new runner\exceptions\cli();
        $runnerExceptionsField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($exceptionColorizer)
            ->setMethodPrompt($exceptionMethodPrompt)
            ->setExceptionPrompt($exceptionPrompt)
        ;

        $this->addField(new fold($runnerExceptionsField, 'exceptions'));

        $uncompletedTestColorizer = new colorizer('0;37');
        $uncompletedTestMethodPrompt = clone $secondLevelPrompt;
        $uncompletedTestMethodPrompt->setColorizer($uncompletedTestColorizer);
        $uncompletedTestOutputPrompt = clone $thirdLevelPrompt;
        $uncompletedTestOutputPrompt->setColorizer($uncompletedTestColorizer);

        $runnerUncompletedField = new runner\tests\uncompleted\cli();
        $runnerUncompletedField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($uncompletedTestColorizer)
            ->setMethodPrompt($uncompletedTestMethodPrompt)
            ->setOutputPrompt($uncompletedTestOutputPrompt)
        ;

        $this->addField(new fold($runnerUncompletedField, 'uncompleted'));

        $voidTestColorizer = new colorizer('0;34');
        $voidTestMethodPrompt = clone $secondLevelPrompt;
        $voidTestMethodPrompt->setColorizer($voidTestColorizer);

        $runnerVoidField = new runner\tests\blank\cli();
        $runnerVoidField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($voidTestColorizer)
            ->setMethodPrompt($voidTestMethodPrompt)
        ;

        $this->addField(new fold($runnerVoidField, 'void'));

        $skippedTestColorizer = new colorizer('0;90');
        $skippedTestMethodPrompt = clone $secondLevelPrompt;
        $skippedTestMethodPrompt->setColorizer($skippedTestColorizer);

        $runnerSkippedField = new runner\tests\skipped\cli();
        $runnerSkippedField
            ->setTitlePrompt($firstLevelPrompt)
            ->setTitleColorizer($skippedTestColorizer)
            ->setMethodPrompt($skippedTestMethodPrompt)
        ;

        $this->addField(new fold($runnerSkippedField, 'skipped'));

        $this->addField(new \mageekguy\atoum\report\fields\test\travis\start());

        $testRunField = new test\run\cli();
        $testRunField
            ->setPrompt($firstLevelPrompt)
            ->setColorizer($defaultColorizer)
        ;

        $this->addField($testRunField);

        $this->addField(new test\event\cli());

        $testDurationField = new test\duration\cli();
        $testDurationField
            ->setPrompt($secondLevelPrompt)
        ;

        $this->addField($testDurationField);

        $testMemoryField = new test\memory\cli();
        $testMemoryField
            ->SetPrompt($secondLevelPrompt)
        ;

        $this->addField($testMemoryField);

        $this->addField(new \mageekguy\atoum\report\fields\test\travis\stop());
    }
}
