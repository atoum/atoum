<?php

namespace mageekguy\atoum\tests\units\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\report,
	mageekguy\atoum\score,
	ageekguy\atoum\asserter\exception,
	mageekguy\atoum\reports\asynchronous as reports
;

require_once __DIR__ . '/../../../runner.php';

class clover extends atoum\test
{
	public function testClass()
	{
		$this
			->testedClass->isSubclassOf('mageekguy\atoum\reports\asynchronous')
		;
	}

	public function testClassConstants()
	{
		$this
			->string(reports\clover::defaultTitle)->isEqualTo('atoum code coverage')
			->string(reports\clover::defaultPackage)->isEqualTo('atoumCodeCoverage')
			->string(reports\clover::lineTypeMethod)->isEqualTo('method')
			->string(reports\clover::lineTypeStatement)->isEqualTo('stmt')
			->string(reports\clover::lineTypeConditional)->isEqualTo('cond')
		;
	}

	public function test__construct()
	{
		$report = new reports\clover();

		$this
			->array($report->getFields(atoum\runner::runStart))->isEmpty()
			->object($report->getAdapter())->isInstanceOf('mageekguy\atoum\adapter')
		;

		$adapter = new atoum\test\adapter();
		$adapter->extension_loaded = true;

		$this
			->if($report = new reports\clover($adapter))
			->then
				->array($report->getFields())->isEmpty()
				->object($report->getAdapter())->isIdenticalTo($adapter)
				->adapter($adapter)->call('extension_loaded')->withArguments('libxml')->once()
		;

		$adapter->extension_loaded = false;

		$this
			->exception(function() use ($adapter) {
					$report = new reports\clover($adapter);
				}
			)
			->isInstanceOf('mageekguy\atoum\exceptions\runtime')
			->hasMessage('libxml PHP extension is mandatory for clover report')
		;
	}

	public function testSetAdapter()
	{
		$report = new reports\clover();

		$this
			->object($report->setAdapter($adapter = new atoum\adapter()))->isIdenticalTo($report)
			->object($report->getAdapter())->isIdenticalTo($adapter)
		;
	}

	public function testHandleEvent()
	{
		$this
			->if($report = new reports\clover())
			->then
				->variable($report->getTitle())->isEqualTo('atoum code coverage')
				->variable($report->getPackage())->isEqualTo('atoumCodeCoverage')
				->castToString($report)->isEmpty()
				->string($report->handleEvent(atoum\runner::runStop, new atoum\runner())->getTitle())->isEqualTo(reports\clover::defaultTitle)
				->castToString($report)->isNotEmpty()
		;

		$report = new reports\clover();

		$this
			->string($report->setTitle($title = uniqid())->handleEvent(atoum\runner::runStop, new atoum\runner())->getTitle())
			->isEqualTo($title);

		$report = new reports\clover();

		$writer = new \mock\mageekguy\atoum\writers\file();
		$writer->getMockController()->write = function($something) use ($writer) { return $writer; };

		$this
			->when(function() use ($report, $writer) { $report->addWriter($writer)->handleEvent(atoum\runner::runStop, new \mageekguy\atoum\runner()); })
				->mock($writer)->call('writeAsynchronousReport')->withArguments($report)->once()
		;
	}

	public function testSetTitle()
	{
		$report = new reports\clover();

		$this
			->object($report->setTitle($title = uniqid()))->isIdenticalTo($report)
			->string($report->getTitle())->isEqualTo($title)
		;
	}

	public function testSetPackage()
	{
		$report = new reports\clover();

		$this
			->object($report->setPackage($package = uniqid()))->isIdenticalTo($report)
			->string($report->getPackage())->isEqualTo($package)
		;
	}

	public function testBuild()
	{
		$adapter = new atoum\test\adapter();
		$adapter->time = 762476400;
		$adapter->uniqid = 'foo';

        $score = new \mock\mageekguy\atoum\score();
        $score->getMockController()->getCoverage = new score\coverage();

		$observable = new \mock\mageekguy\atoum\runner();
		$observable->getMockController()->getScore = $score;

		$report = new \mock\mageekguy\atoum\reports\asynchronous\clover($adapter);
		$filepath = implode(
			DIRECTORY_SEPARATOR,
			array(
				__DIR__,
				'clover',
				'resources',
				'clover.xml'
			)
		);

		$this
			->if($report->handleEvent(atoum\runner::runStop, $observable))
			->then
				->castToString($report)->isEqualToContentsOfFile($filepath);

        $coverage = new \mock\mageekguy\atoum\score\coverage();
        $coverage->getMockController()->getClasses = array(
            'foo'   => 'foo.php',
            'bar'   => 'bar.php'
        );
        $coverage->getMockController()->getCoverageForClass = function($class) {
            switch($class) {
                case 'foo':
                    return array(
                        array(
                            3 => -2,
                            4 => 1,
                            5 => -1,
                            6 => -1,
                            7 => -1,
                            8 => -2,
                            9 => -1
                        ),
                        array(
                            11 => 1,
                            12 => -1,
                            13 => -2,
                            14 => -1,
                            15 =>  1
                        )
                    );

                case 'bar':
                    return array(
                        array(
                            5 => 2,
                            6 => 3,
                            7 => 4,
                            8 => 3,
                            9 => 2
                        )
                    );
            }

            return array();
        };

        $score->getMockController()->getCoverage = $coverage;

        $filepath = implode(
            DIRECTORY_SEPARATOR,
            array(
                __DIR__,
                'clover',
                'resources',
                'clover-data.xml'
            )
        );

        $this
            ->if($report->handleEvent(atoum\runner::runStop, $observable))
            ->then
                ->castToString($report)->isEqualToContentsOfFile($filepath)
		;
	}
}

?>
