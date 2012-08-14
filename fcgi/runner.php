<?php

namespace mageekguy\atoum\fcgi;

if (
		isset($_POST['atoumDirectory']) === false
	|| isset($_POST['testPath']) === false
	|| isset($_POST['testClass']) === false
	|| isset($_POST['testMethod']) === false
	|| isset($_POST['localeClass']) === false
	|| isset($_POST['localeValue']) === false
	|| isset($_POST['phpPath']) === false
)
{
	die();
}

ob_start();

require $_POST['atoumDirectory'] . '/classes/autoloader.php';

if (isset($_POST['bootstrapFile']) === true)
{
	$includer = new mageekguy\atoum\includer();

	try
	{
		$includer->includePath($_POST['bootstrapFile']);
	}
	catch (mageekguy\atoum\includer\exception $exception)
	{
		die('Unable to include bootstrap file \'' . $_POST['bootstrapFile'] . '\'');
	}
}

require $_POST['testPath'];

$test = new $_POST['testClass']();
$test
	->setLocale(new $_POST['localeClass']($_POST['localeValue']))
	->setPhpPath($_POST['phpPath'])
;

if (isset($_POST['disableCodeCoverage']) === true)
{
	$test->disableCodeCoverage();
}
else
{
	$coverage = $test->getCoverage();

	if (isset($_POST['excludedClasses']) === true && is_array($_POST['excludedClasses']) === true)
	{
		foreach ($_POST['excludedClasses'] as $excludedClass)
		{
			$coverage->excludeClass($excludedClass);
		}
	}

	if (isset($_POST['excludedNamespaces']) === true && is_array($_POST['excludedNamespaces']) === true)
	{
		foreach ($_POST['excludedNamespaces'] as $excludedNamespace)
		{
			$coverage->excludeNamespace($excludedNamespace);
		}
	}

	if (isset($_POST['excludedDirectories']) === true && is_array($_POST['excludedDirectories']) === true)
	{
		foreach ($_POST['excludedDirectories'] as $excludedDirectory)
		{
			$coverage->excludeNamespace($excludedDirectory);
		}
	}
}

$test->setMethodTags($_POST['testMethod'], array('engine' => 'inline'));

\mageekguy\atoum\scripts\runner::disableAutorun();

ob_end_clean();

echo serialize($test->runTestMethod($_POST['testMethod'])->getScore());
