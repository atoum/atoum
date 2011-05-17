<?php

namespace mageekguy\atoum\reports\asynchronous\coverage;

use
	\mageekguy\atoum,
	\mageekguy\atoum\reports,
	\mageekguy\atoum\template
;

class html extends reports\asynchronous
{
	protected $templatesDirectory = null;
	protected $destinationDirectory = null;
	protected $templateParser = null;

	public function __construct($templatesDirectory, $destinationDirectory, template\parser $parser = null, atoum\locale $locale = null, atoum\adapter $adapter = null)
	{
		parent::__construct($locale, $adapter);

		if ($parser === null)
		{
			$parser = new template\parser();
		}

		$this
			->setTemplatesDirectory($templatesDirectory)
			->setDestinationDirectory($destinationDirectory)
			->setTemplateParser($parser)
		;
	}

	public function setTemplatesDirectory($path)
	{
		$this->templatesDirectory = (string) $path;

		return $this;
	}

	public function getTemplatesDirectory()
	{
		return $this->templatesDirectory;
	}

	public function setDestinationDirectory($path)
	{
		$this->destinationDirectory = (string) $path;

		return $this;
	}

	public function getDestinationDirectory()
	{
		return $this->destinationDirectory;
	}

	public function setTemplateParser(template\parser $parser)
	{
		$this->templateParser = $parser;

		return $this;
	}

	public function getTemplateParser()
	{
		return $this->templateParser;
	}

	public function runnerStop(atoum\runner $runner)
	{
		$coverage = $runner->getScore()->getCoverage();

		if (sizeof($coverage) > 0)
		{
		}

		return $this;
	}
}

?>
