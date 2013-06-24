<?php

namespace mageekguy\atoum\reports\asynchronous;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions,
	mageekguy\atoum\score
;

class coveralls extends atoum\reports\asynchronous
{
	const defaultServiceName = 'atoum';
	const defaultEvent = 'manual';
	const defaultCoverallsUrl = 'https://coveralls.io/api/v1/jobs';

	protected $sourceDir = null;
	protected $repositoryToken = null;
	protected $score = null;
	protected $branchFinder;
	protected $serviceName;
	protected $serviceJobId;

	public function __construct($sourceDir, $repositoryToken, atoum\adapter $adapter = null)
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

	public function getSourceDir()
	{
		return $this->sourceDir;
	}

	public function handleEvent($event, atoum\observable $observable)
	{
		$this->score = ($event !== atoum\runner::runStop ? null : $observable->getScore());

		return parent::handleEvent($event, $observable);
	}

	public function build($event)
	{
		if ($event === atoum\runner::runStop)
		{
			$coverage = $this->makeJson($this->score->getCoverage());
			$this->string = json_encode($coverage);

			if (sizeof($coverage['source_files']) > 0)
			{
				$opts = array(
					'http' => array(
						'method'  => 'POST',
						'header'  => 'Content-type: multipart/form-data',
						'content' => http_build_query(array('json' => $this->string))
					)
				);
				$context = stream_context_create($opts);
				$this->adapter->file_get_contents(static::defaultCoverallsUrl, false, $context);
			}
		}

		return $this;
	}

	protected function makeJson(score\coverage $coverage)
	{
		return array(
			'service_name' => $this->serviceName,
			'service_event_type' => static::defaultEvent,
			'service_job_id' => $this->serviceJobId,
			'repo_token' => $this->repositoryToken,
			'run_at' => $this->adapter->date('Y-m-d H:i:s O'),
			'source_files' => $this->makeSourceElement($coverage),
			'git' => $this->makeGitElement()
		);
	}

	protected function makeGitElement()
	{
		$head = $this->adapter->exec('git log -1 --pretty=format:\'{"id":"%H","author_name":"%aN","author_email":"%ae","committer_name":"%cN","committer_email":"%ce","message":"%s"}\'');
		$infos = array('head' => json_decode($head));

		$branch = call_user_func($this->getBranchFinder());
		if (isset($branch))
		{
			$infos['branch'] = trim($branch, '* ');
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
				'name' => ltrim((string) $path->relativizeFrom($this->sourceDir), './'),
				'source' => $source,
				'coverage' => $this->makeCoverageElement($coverage->getCoverageForClass($class))
			);
		}

		return $sources;
	}

	protected function makeCoverageElement(array $coverage)
	{
		$cover = array();

		foreach($coverage as $method)
		{
			foreach ($method as $number => $line)
			{
				if ($number > 1)
				{
					while (count($cover) < ($number - 1))
					{
						$cover[] = null;
					}
				}

				if ($line === 1)
				{
					$cover[] = 1;
				}
				else
				{
					$cover[] = 0;
				}
			}
		}

		return $cover;
	}
}
