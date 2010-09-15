<?php

namespace mageekguy\atoum;

use \mageekguy\atoum;

require(__DIR__ . '/autoloader.php');

class runner implements observable
{
	const runStart = 'runnerStart';
	const runStop = 'runnerStop';

	protected $observers = array();
	protected $configure = null;
	protected $configureTest = null;

	public function addObserver(atoum\observers\runner $observer)
	{
		$this->observers[] = $observer;
		return $this;
	}

	public function getObservers()
	{
		return $this->observers;
	}

	public function callObservers($method)
	{
		foreach ($this->observers as $observer)
		{
			$observer->{$method}($this);
		}

		return $this;
	}

	public function testIsConfigured()
	{
		return ($this->configureTest !== null);
	}

	public function configureTestWith(\closure $configureTest)
	{
		$this->configureTest = $configureTest;

		return $this;
	}

	public function isConfigured()
	{
		return ($this->configure !== null);
	}

	public function configureWith(\closure $configure)
	{
		$this->configure = $configure;

		return $this;
	}

	public function run($testClass = '\mageekguy\atoum\test')
	{
		$this->configure->__invoke($this);

		$this->callObservers(self::runStart);

		foreach (array_filter(get_declared_classes(), function($class) use ($testClass) { return (is_subclass_of($class, $testClass) === true && get_parent_class($class) !== false); }) as $class)
		{
			$test = new $class();

			$this->configureTest->__invoke($test);

			$test->run();
		}

		$this->callObservers(self::runStop);
	}

	public static function getInstance()
	{
		static $instance = null;

		if ($instance === null)
		{
			$instance = new self();
		}

		return $instance;
	}

	protected function __construct() {}
}

?>
