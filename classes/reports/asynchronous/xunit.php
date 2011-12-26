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

	public function __construct(atoum\adapter $adapter = null)
	{
		parent::__construct(null, $adapter);

		if ($this->adapter->extension_loaded('libxml') === false)
		{
			throw new exceptions\runtime('libxml PHP extension is mandatory for xunit report');
		}
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		$this->score = ($event !== atoum\runner::runStop) ? null : $observable->getScore();

		return parent::handleEvent($event, $observable);
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
			$durations = $this->score->getDurations();
			$errors = $this->score->getErrors();
			$excepts = $this->score->getExceptions();
			$fails = $this->score->getFailAssertions();

			$filterClass = function ($element) use (& $clname) { return ($element['class'] == $clname); };

			$classes = array();

			foreach ($durations as $duration)
			{
				if (isset($classes[$duration['class']]) === false)
				{
					$clname = $duration['class'];
					$classes[$clname] = array(
						'errors' => array_filter($errors, $filterClass),
						'excepts' => array_filter($excepts, $filterClass),
						'fails' => array_filter($fails, $filterClass),
						'durations' => array_filter($durations, $filterClass)
					);
				}
			}

			$filterMethod = function ($element) use (& $method) { return ($element['method'] == $method); };

			foreach ($classes as $name => $class)
			{
				$antiSlashOffset = strrpos($name, '\\');
				$clname = substr($name, $antiSlashOffset + 1);

				$root->appendChild($testSuite  = $document->createElement('testsuite'));

				$testSuite->setAttribute('name', $clname);
				$testSuite->setAttribute('package', substr($name, 0, $antiSlashOffset));
				$testSuite->setAttribute('tests', sizeof($class['durations']));
				$testSuite->setAttribute('failures', sizeof($class['fails']));
				$testSuite->setAttribute('errors', sizeof($class['excepts']) + sizeof($class['errors']));

				$time = 0;

				foreach ($class['durations'] as $duration)
				{
					$time += $duration['value'];

					$testSuite->appendChild($testCase = $document->createElement('testcase'));

					$testCase->setAttribute('name', $duration['method']);
					$testCase->setAttribute('time', $duration['value']);
					$testCase->setAttribute('classname', $name);

					foreach (array_filter($class['fails'], $filterMethod) as $fail)
					{
						$testCase->appendChild($xFail = $document->createElement('failure', $fail['fail']));

						$xFail->setAttribute('type','Assertion Fail');
						$xFail->setAttribute('message', $fail['asserter']);
					}

					foreach (array_filter($class['excepts'], $filterMethod) as $except)
					{
						$testCase->appendChild($xError = $document->createElement('error'));

						$xError->setAttribute('type','Exception');
						$xError->appendChild($document->createCDATASection($except['value']));
					}
				}

				$testSuite->setAttribute('time', $time);

				foreach ($class['errors'] as $error)
				{
					$testSuite->appendChild($testCase = $document->createElement('testcase'));

					$testCase->setAttribute('name', $error['method']);
					$testCase->setAttribute('time', '0');
					$testCase->setAttribute('classname', $name);

					$testCase->appendChild($xError = $document->createElement('error'));

					$xError->setAttribute('type', $error['type']);
					$xError->appendChild($document->createCDATASection($cError['message']));
				}
			}

			$this->string = $document->saveXML();
		}

		return $this;
	}
}

?>
