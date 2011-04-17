<?php

namespace mageekguy\atoum;

class report implements observers\runner, observers\test
{
	protected $title = null;
	protected $locale = null;
	protected $adapter = null;
	protected $writers = array();
	protected $testFields = array();
	protected $runnerFields = array();

	private $lastSetFields = array();
	private $lastEventValue = null;
	private $lastEventTransmitter = null;

	public function __construct(locale $locale = null, adapter $adapter = null)
	{
		$this->runnerFields = array(
			runner::runStart => array(),
			runner::runStop => array()
		);

		$this->testFields = array(
			test::runStart => array(),
			test::beforeSetUp => array(),
			test::afterSetUp => array(),
			test::beforeTestMethod => array(),
			test::success => array(),
			test::fail => array(),
			test::error => array(),
			test::exception => array(),
			test::afterTestMethod => array(),
			test::beforeTearDown => array(),
			test::afterTearDown => array(),
			test::runStop => array(),
		);

		if ($locale === null)
		{
			$locale = new locale();
		}

		$this->setLocale($locale);

		if ($adapter === null)
		{
			$adapter = new adapter();
		}

		$this->setAdapter($adapter);
	}

	public function setTitle($title)
	{
		$this->title = (string) $title;

		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function setLocale(locale $locale)
	{
		$this->locale = $locale;

		return $this;
	}

	public function getLocale()
	{
		return $this->locale;
	}


	public function setAdapter(adapter $adapter)
	{
		$this->adapter = $adapter;

		return $this;
	}

	public function getAdapter()
	{
		return $this->adapter;
	}

	public function addRunnerField(report\fields\runner $field, array $events = array())
	{
		return $this->addField($field->setLocale($this->locale), $events, 'runnerFields');
	}

	public function addTestField(report\fields\test $field, array $events = array())
	{
		return $this->addField($field->setLocale($this->locale), $events, 'testFields');
	}

	public function getRunnerFields($event = null)
	{
		$fields = array();

		if ($event === null)
		{
			$fields = $this->runnerFields;
		}
		else
		{
			if (in_array($event, runner::getObserverEvents()) === false)
			{
				throw new exceptions\logic\invalidArgument('\'' . $event . '\' is not a runner event');
			}

			$fields = $this->runnerFields[$event];
		}

		return $fields;
	}

	public function getTestFields($event = null)
	{
		$fields = array();

		if ($event === null)
		{
			$fields = $this->testFields;
		}
		else
		{
			if (in_array($event, test::getObserverEvents()) === false)
			{
				throw new exceptions\logic\invalidArgument('\'' . $event . '\' is not a test event');
			}

			$fields = $this->testFields[$event];
		}

		return $fields;
	}

	public function getLastEventValue()
	{
		return $this->lastEventValue;
	}

	public function getLastEventTransmitter()
	{
		return $this->lastEventTransmitter;
	}

	public function lastEventIsRunnerEvent($event = null)
	{
		return ($this->lastEventTransmitter instanceof runner && ($event === null || $this->lastEventValue === $event));
	}

	public function lastEventIsTestEvent($event = null)
	{
		return ($this->lastEventTransmitter instanceof test && ($event === null || $this->lastEventValue === $event));
	}

	public function getWriters()
	{
		return $this->writers;
	}

	public function runnerStart(runner $runner)
	{
		return $this->setRunnerFields($runner, __FUNCTION__);
	}

	public function testRunStart(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function beforeTestSetUp(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function afterTestSetUp(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function beforeTestMethod(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function testAssertionSuccess(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function testAssertionFail(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function testError(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function testException(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function afterTestMethod(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function testRunStop(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function beforeTestTearDown(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function afterTestTearDown(test $test)
	{
		return $this->setTestFields($test, __FUNCTION__);
	}

	public function runnerStop(runner $runner)
	{
		return $this->setRunnerFields($runner, __FUNCTION__);
	}

	public function __toString()
	{
		$string = '';

		foreach ($this->lastSetFields as $field)
		{
			$string .= $field;
		}

		return $string;
	}

	protected function doAddWriter($writer)
	{
		$this->writers[] = $writer;

		return $this;
	}

	protected function addField(report\field $field, array $events, $propertyName)
	{
		if (sizeof($events) <= 0)
		{
			foreach ($this->{$propertyName} as & $fields)
			{
				$fields[] = $field;
			}
		}
		else
		{
			foreach ($events as $event)
			{
				if (isset($this->{$propertyName}[$event]) === false)
				{
					throw new exceptions\runtime('Event \'' . $event . '\' does not exist');
				}

				$this->{$propertyName}[$event][] = $field;
			}
		}

		return $this;
	}

	private function setRunnerFields(runner $runner, $event)
	{
		$this->lastSetFields = array();
		$this->lastEventValue = $event;
		$this->lastEventTransmitter = $runner;

		if (isset($this->runnerFields[$event]) === true)
		{
			foreach ($this->runnerFields[$event] as $field)
			{
				$field->setWithRunner($runner, $event);
			}

			$this->lastSetFields = $this->runnerFields[$event];
		}

		return $this;
	}

	private function setTestFields(test $test, $event)
	{
		$this->lastSetFields = array();
		$this->lastEventValue = $event;
		$this->lastEventTransmitter = $test;

		if (isset($this->testFields[$event]) === true)
		{
			foreach ($this->testFields[$event] as $field)
			{
				$field->setWithTest($test, $event);
			}

			$this->lastSetFields = $this->testFields[$event];
		}

		return $this;
	}
}

?>
