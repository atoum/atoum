<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\score,
	mageekguy\atoum\report
;

class coveralls extends atoum\reports\asynchronous
{
	const defaultServiceName = 'atoum';
	const defaultEvent = 'manual';
	const defaultCoverallsApiUrl = 'https://coveralls.io/api/v1/jobs';
	const defaultCoverallsApiMethod = 'POST';
	const defaultCoverallsApiParameter = 'json';

	protected $sourceDir = null;
	protected $repositoryToken = null;
	protected $score = null;
	protected $branchFinder;
	protected $serviceName;
	protected $serviceJobId;

	public function __construct($sourceDir, $repositoryToken = null, atoum\adapter $adapter = null)
	{
		parent::__construct();

		$this
			->setAdapter($adapter)
			->setBranchFinder()
			->setServiceName()
		;

		if ($this->adapter->extension_loaded('json') === false)
		{
			throw new exceptions\runtime('JSON PHP extension is mandatory for coveralls report');
		}

		$this->repositoryToken = $repositoryToken;
		$this->sourceDir = new atoum\fs\path($sourceDir);
	}

	public function setBranchFinder(\closure $finder = null)
	{
		$adapter = $this->adapter;

		$this->branchFinder = $finder ?: function() use ($adapter) { return $adapter->exec('git rev-parse --abbrev-ref HEAD'); };

		return $this;
	}

	public function getBranchFinder()
	{
		return $this->branchFinder;
	}

	public function setServiceName($name = null)
	{
		$this->serviceName = $name ?: static::defaultServiceName;

		return $this;
	}

	public function getServiceName()
	{
		return $this->serviceName;
	}

	public function setServiceJobId($id = null)
	{
		$this->serviceJobId = $id;

		return $this;
	}

	public function getServiceJobId()
	{
		return $this->serviceJobId;
	}

	public function addDefaultWriter(atoum\writers\http $writer = null)
	{
		$writer = $writer ?: new atoum\writers\http($this->adapter);
		$writer
			->setUrl(static::defaultCoverallsApiUrl)
			->setMethod(static::defaultCoverallsApiMethod)
			->setParameter(static::defaultCoverallsApiParameter)
			->addHeader('Content-Type', 'multipart/form-data')
		;

		return parent::addWriter($writer);
	}

	public function getSourceDir()
	{
		return $this->sourceDir;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		$this->score = ($event !== atoum\runner::runStop ? null : $observable->getScore());

		try
		{
			return parent::handleEvent($event, $observable);
		}
		catch (atoum\writers\http\exception $exception)
		{
			return $this;
		}
	}

	public function build($event)
	{
		if ($event === atoum\runner::runStop)
		{
			$coverage = $this->makeRootElement($this->score->getCoverage());
			$this->string = json_encode($coverage);
		}

		return $this;
	}

	protected function makeRootElement(score\coverage $coverage)
	{
		$root = array(
			'service_name' => $this->serviceName,
			'service_event_type' => static::defaultEvent,
			'service_job_id' => $this->serviceJobId,
			'run_at' => $this->adapter->date('Y-m-d H:i:s O'),
			'source_files' => $this->makeSourceElement($coverage),
			'git' => $this->makeGitElement()
		);

		if ($this->repositoryToken !== null)
		{
			$root['repo_token'] = $this->repositoryToken;
		}

		return $root;
	}

	protected function makeGitElement()
	{
		$head = $this->adapter->exec('git log -1 --pretty=format:\'{"id":"%H","author_name":"%aN","author_email":"%ae","committer_name":"%cN","committer_email":"%ce","message":"%s"}\'');
		$infos = array('head' => json_decode($head));

		$branch = call_user_func($this->getBranchFinder());
		if ($branch != null)
		{
			$infos['branch'] = $branch;
		}

		return $infos;
	}

	protected function makeSourceElement(score\coverage $coverage)
	{
		$sources = array();

		foreach ($coverage->getClasses() as $class => $file)
		{
			$path = new atoum\fs\path($file);
			$source = $this->adapter->file_get_contents((string) $path->resolve());

			$sources[] = array(
				'name' => ltrim((string) $path->relativizeFrom($this->sourceDir), '.' . DIRECTORY_SEPARATOR),
				'source' => $source,
				'coverage' => $this->makeCoverageElement($coverage->getCoverageForClass($class))
			);
		}

		return $sources;
	}

	protected function makeCoverageElement(array $coverage)
	{
		$cover = array();

		foreach ($coverage as $method)
		{
			foreach ($method as $number => $line)
			{
				if ($number > 1)
				{
					while (sizeof($cover) < ($number - 1))
					{
						$cover[] = null;
					}
				}

				if ($line === 1)
				{
					$cover[] = 1;
				}
				elseif ($line >= -1)
				{
					$cover[] = 0;
				}
			}
		}

		return $cover;
	}
}
