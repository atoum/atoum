<?php

/*
Sample atoum configuration file to have code coverage in html format.
Do "php path/to/test/file -c path/to/this/file" or "php path/to/atoum/scripts/runner.php -c path/to/this/file -f path/to/test/file" to use it.
*/

use \mageekguy\atoum;

$coverageHtmlField = new atoum\report\fields\runner\coverage\html(
    'Code coverage of atoum',
    '/Users/fch/Sites/atoum/coverage'
);

$coverageHtmlField
	->addSrcDirectory(__DIR__ . '/classes')
	->setRootUrl('http://localhost/~fch/atoum/coverage/')
;

/*
Please replace in next line /path/to/destination/directory by your destination directory path for html files.
*/
$coverageTreemapField = new atoum\report\fields\runner\coverage\treemap('atoum', '/Users/fch/Sites/atoum/d3/');

/*
Please replace in next line http://url/of/web/site by the root url of your code coverage web site.
*/
$coverageTreemapField
	->setTreemapUrl('http://localhost/~fch/coverage/d3')
	->addSrcDirectory(__DIR__ . '/classes')
	->setHtmlReportBaseUrl($coverageHtmlField->getRootUrl())
;

$script
	->addDefaultReport()
		->addField($coverageHtmlField)
		->addField($coverageTreemapField)
;
