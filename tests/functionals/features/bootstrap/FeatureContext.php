<?php

use
	Behat\Behat\Context\ClosuredContextInterface,
	Behat\Behat\Context\TranslatedContextInterface,
	Behat\Behat\Context\BehatContext,
	Behat\Behat\Exception\PendingException,
	Behat\Gherkin\Node\PyStringNode,
	Behat\Gherkin\Node\TableNode,
	mageekguy\atoum
;

require_once __DIR__ . '/../../../../classes/autoloader.php';

class FeatureContext extends BehatContext
{
	protected $assert = null;
	protected $output = null;
	protected $workingDirectory = null;

	public function __construct(array $parameters)
	{
		$this->assert = new atoum\asserter\generator();
		$this->workingDirectory = __DIR__ . '/../../tmp';

		chdir($this->workingDirectory);

		@unlink($this->workingDirectory . '/' . atoum\scripts\phar\generator::phar);

		exec('php -d phar.readonly=0 ' . atoum\directory . '/scripts/phar/generator.php -d ' . $this->workingDirectory);
	}

	/**
	 * @Given /^i have an atoum PHAR archive$/
	 */
	public function iHaveAnAtoumPharArchive()
	{
		$this->assert
			->boolean(is_file($this->workingDirectory . '/' . atoum\scripts\phar\generator::phar))->isTrue('Unable to generate PHAR archive in \'' . $this->workingDirectory . '\'')
		;
	}

	/**
	 * @When /^i run atoum PHAR archive with "([^"]*)" argument$/
	 */
	public function iRunAtoumPharArchiveWithArgument($argument)
	{
		ob_start();

		passthru(escapeshellcmd('php ' . $this->workingDirectory . '/' . atoum\scripts\phar\generator::phar . ' ' . $argument));

		$this->output = ob_get_clean();
	}

	/**
	 * @Then /^the output must match "([^"]*)"$/
	 */
	public function theOutputMustMatch($regex)
	{
		$this->assert
			->string($this->output)->match($regex)
		;
	}

	/**
	 * @Given /^i have a configuration file "([^"]*)" which contents "([^"]*)"$/
	 */
	public function iHaveAConfigurationFileWhichContents($file, $contents)
	{
		file_put_contents($this->workingDirectory . '/' . $file, $contents);
	}
}
