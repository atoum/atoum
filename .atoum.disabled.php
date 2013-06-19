<?php

$coveralls = new \mageekguy\atoum\reports\asynchronous\coveralls('classes', 'V2GUFBhNrmYvv1Dc7rZaqzJfnplZIDqyH');
$defaultFinder = $coveralls->getBranchFinder();
$coveralls
	->setBranchFinder(function() use ($defaultFinder) {
		if (($branch = getenv('TRAVIS_BRANCH')) === false)
		{
			$branch = $defaultFinder();
		}

		return $branch;
	})
	->setServiceName(getenv('TRAVIS') ? 'travis-ci' : null)
	->setServiceJobId(getenv('TRAVIS_JOB_ID') ?: null)
	->addWriter()
;
$runner->addReport($coveralls);

$xunit = new \mageekguy\atoum\reports\asynchronous\xunit();
$runner->addReport($xunit->addWriter(new \mageekguy\atoum\writers\file('xunit.xml')));

$script->addDefaultReport();
