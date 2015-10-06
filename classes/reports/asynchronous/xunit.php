<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\report\fields
;

class xunit extends atoum\reports\asynchronous
{
	const defaultTitle = 'atoum testsuite';

	protected $score = null;
	protected $assertions = array();

	public function __construct(atoum\adapter $adapter = null)
	{
		parent::__construct();

		$this->setAdapter($adapter);

		if ($this->adapter->extension_loaded('libxml') === false)
		{
			throw new exceptions\runtime('libxml PHP extension is mandatory for xunit report');
		}
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		$this->score = null;

		if ($event === atoum\test::afterTestMethod)
		{
			$classname = $this->adapter->get_class($observable);
			$method = $observable->getCurrentMethod();

			if (isset($this->assertions[$classname]) === false)
			{
				$this->assertions[$classname] = array();
			}

			$this->assertions[$classname][$method] = $observable->getScore()->getAssertionNumber() - array_sum($this->assertions[$classname]);
		}

		if ($event === atoum\runner::runStop)
		{
			$this->score = $observable->getScore();
		}

		return parent::handleEvent($event, $observable);
	}

	protected function getTestedClasses()
	{
		$durations = $this->score->getDurations();
		$errors = $this->score->getErrors();
		$excepts = $this->score->getExceptions();
		$fails = $this->score->getFailAssertions();
		$uncomplete = $this->score->getUncompletedMethods();
		$skipped = $this->score->getSkippedMethods();
		$assertions = $this->assertions;

		$filterClass = function($element) use (& $clname) { return ($element['class'] == $clname); };
		$extractClasses = function($list) use (& $clname, & $classes, & $assertions, $durations, $errors, $excepts, $fails, $uncomplete, $skipped, $filterClass) {
			foreach ($list as $entry)
			{
				$clname = ltrim($entry['class'], '\\');

				if (isset($classes[$clname]) === false)
				{
					$classes[$clname] = array(
						'errors' => array_filter($errors, $filterClass),
						'excepts' => array_filter($excepts, $filterClass),
						'fails' => array_filter($fails, $filterClass),
						'durations' => array_filter($durations, $filterClass),
						'uncomplete' => array_filter($uncomplete, $filterClass),
						'skipped' => array_filter($skipped, $filterClass),
						'assertions' => isset($assertions[$clname]) ? $assertions[$clname] : array()
					);
				}
			}
		};

		$classes = array();
		$extractClasses($durations);
		$extractClasses($errors);
		$extractClasses($excepts);
		$extractClasses($fails);
		$extractClasses($uncomplete);
		$extractClasses($skipped);

		return $classes;
	}

	public function build($event)
	{
		$this->string = '';

		if ($event === atoum\runner::runStop)
		{
			$this->title = $this->title ?: self::defaultTitle;

			$document = new \DOMDocument('1.0', 'UTF-8');
			$document->formatOutput = true;
			$document->appendChild($root = $document->createElement('testsuites'));
			$root->setAttribute('name', $this->title);
			$classes = $this->getTestedClasses();

			foreach ($classes as $name => $class)
			{
				$clname = $package = $name;
				$antiSlashOffset = strrpos($clname, '\\');
				if ($antiSlashOffset !== false)
				{
					$clname = substr($clname, $antiSlashOffset + 1);
					$package = substr($name, 0, $antiSlashOffset);
				}

				$root->appendChild($testSuite = $document->createElement('testsuite'));

				$testSuite->setAttribute('name', $clname);
				$testSuite->setAttribute('package', $package);
				$testSuite->setAttribute('tests', sizeof($class['durations']) + ($fails = sizeof($class['fails'])) + ($errors = sizeof($class['excepts']) + sizeof($class['errors']) + sizeof($class['uncomplete'])) + sizeof($class['skipped']));
				$testSuite->setAttribute('failures', $fails);
				$testSuite->setAttribute('errors', $errors);
				$testSuite->setAttribute('skipped', sizeof($class['skipped']));

				$time = 0;
				foreach ($class['durations'] as $duration)
				{
					$time += $duration['value'];

					self::getTestCase($document, $testSuite, $name, $duration['method'], $duration['value'], $duration['path'], isset($class['assertions'][$duration['method']]) ? $class['assertions'][$duration['method']] : 0);
				}

				$testSuite->setAttribute('time', $time);

				foreach ($class['errors'] as $error)
				{
					$testCase = self::getTestCase($document, $testSuite, $name, $error['method'], 0, $error['file'], isset($class['assertions'][$error['method']]) ? $class['assertions'][$error['method']] : 0);
					$testCase->appendChild($xError = $document->createElement('error'));

					$xError->setAttribute('type', $error['type']);
					$xError->appendChild($document->createCDATASection($error['message']));
				}

				foreach ($class['uncomplete'] as $uncomplete)
				{
					$testCase = self::getTestCase($document, $testSuite, $name, $uncomplete['method'], 0, null, isset($class['assertions'][$uncomplete['method']]) ? $class['assertions'][$uncomplete['method']] : 0);
					$testCase->appendChild($xFail = $document->createElement('error'));

					$xFail->setAttribute('type', $uncomplete['exitCode']);
					$xFail->appendChild($document->createCDATASection($uncomplete['output']));
				}

				foreach ($class['fails'] as $fail)
				{
					$testCase = self::getTestCase($document, $testSuite, $name, $fail['method'], 0, $fail['file'], isset($class['assertions'][$fail['method']]) ? $class['assertions'][$fail['method']] : 0);
					$testCase->appendChild($xFail = $document->createElement('failure'));

					$xFail->setAttribute('type', 'Failure');
					$xFail->setAttribute('message', $fail['asserter']);

					$xFail->appendChild($document->createCDATASection($fail['fail']));
				}

				foreach ($class['excepts'] as $exc)
				{
					$testCase = self::getTestCase($document, $testSuite, $name, $exc['method'], 0, $exc['file'], isset($class['assertions'][$exc['method']]) ? $class['assertions'][$exc['method']] : 0);
					$testCase->appendChild($xError = $document->createElement('error'));

					$xError->setAttribute('type', 'Exception');
					$xError->appendChild($document->createCDATASection($exc['value']));
				}

				foreach ($class['skipped'] as $skipped)
				{
					$testCase = self::getTestCase($document, $testSuite, $name, $skipped['method'], 0, null, isset($class['assertions'][$skipped['method']]) ? $class['assertions'][$skipped['method']] : 0);
					$testCase->appendChild($xFail = $document->createElement('skipped'));

					$xFail->setAttribute('type', 'Skipped');

					$xFail->appendChild($document->createCDATASection($skipped['message']));
				}
			}

			$this->string = $document->saveXML();
		}

		return $this;
	}

	private static function getTestCase(\DOMDocument $document, \DOMElement $testSuite, $class, $method, $time, $path, $assertions)
	{
		if (($testCase = self::findTestCase($document, $class, $method)) === null)
		{
			$testCase = $document->createElement('testcase');
			$testCase->setAttribute('name', $method);

			set_error_handler(function() {}, E_WARNING);

			$testCase->setIdAttribute('name', true);

			restore_error_handler();

			$testCase->setAttribute('time', $time);
			$testCase->setAttribute('classname', $class);
			$testCase->setAttribute('assertions', $assertions);

			$testSuite->appendChild($testCase);
		}

		return $testCase;
	}

	private static function findTestCase(\DOMDocument $document, $class, $method)
	{
		$xpath = new \DOMXPath($document);
		$query = $xpath->query("//testcase[@classname='$class' and @name='$method']");

		if ($query->length > 0)
		{
			return $query->item(0);
		}

		return null;
	}
}
