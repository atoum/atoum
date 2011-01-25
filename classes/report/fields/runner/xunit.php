<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class xunit extends report\fields\runner
{
	protected $score = null;

	public function setWithRunner(atoum\runner $runner, $event = null)
	{
		if ($event === atoum\runner::runStop)
		{
			$this->score = $runner->getScore();
		}

		return $this;
	}

	public function __toString()
	{
		$string = '';

		if ($this->score != null)
		{
			$document = new \DOMDocument('1.0', 'UTF-8');
			$document->formatOutput = true;
			$document->appendChild($root = $document->createElement('testSuites'));

			$durations = $this->score->getDurations();
			$errors = $this->score->getErrors();
			$excepts = $this->score->getExceptions();
			$fails = $this->score->getFailAssertions();

			$filterClass = function ($element) use (& $class) { return ($element['class'] == $class); };
			$filterMethod = function ($element) use (& $method) { return ($element['method'] == $method); };

			$classes = array();

			foreach ($durations as $duration)
			{
				if (isset($classes[$duration['class']]) === false)
				{
					$classes[$duration['class']] = array(
						'errors' => array_filter($errors, $filterClass),
						'excepts' => array_filter($excepts, $filterClass),
						'fails' => array_filter($fails, $filterClass),
						'durations' => array_filter($durations, $filterClass)
					);
				}
			}

			foreach ($classes as $name => $class)
			{
				$antiSlashOffset = strrpos($name, '\\');
				$name = substr($name, $antiSlashOffset + 1);
				$package = substr($name, 0, $antiSlashOffset);

				$root->appendChild($testSuite  = $document->createElement('testSuite'));

				$testSuite->setAttribute('name', $name);
				$testSuite->setAttribute('package', $package);
				$testSuite->setAttribute('tests', sizeof($class['durations']));
				$testSuite->setAttribute('failures', sizeof($class['fails']));
				$testSuite->setAttribute('errors', sizeof($class['excepts']) + sizeof($class['errors']));

				$time = 0;

				foreach ($class['durations'] as $duration)
				{
					$time += $duration['value'];

					$method = $duration['method'];

					$testSuite->appendChild($testCase = $document->createElement('testCase'));

					$testCase->setAttribute('name', $method);
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
					$method = $error['method'];

					$testSuite->appendChild($testCase = $document->createElement('testCase'));

					$testCase->setAttribute('name', $methName);
					$testCase->setAttribute('time', '0');
					$testCase->setAttribute('classname', $name);

					$testCase->appendChild($xError = $document->createElement('error'));

					$xError->setAttribute('type', $error['type']);
					$xError->appendChild($document->createCDATASection($cError['message']));
				}
			}

			$tring = $document->saveXML();
		}

		return $string;
	}
}

?>
