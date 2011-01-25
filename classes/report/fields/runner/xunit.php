<?php

namespace mageekguy\atoum\report\fields\runner;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class xunit extends report\fields\runner
{
   /**
    * Runner score
    * @var mageekguy\atoum\score
    */
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
	   $document = new \DOMDocument('1.0', 'UTF-8');
      $document->formatOutput = TRUE;
        
      $root = $document->createElement('testsuites');
      $document->appendChild($root);
      
      $durations = $this->score->getDurations();
      $errors = $this->score->getErrors();
      $excepts = $this->score->getExceptions();
      $fails = $this->score->getFailAssertions();
      
      $classes = array();
      $treated = array();
      $currentclass = array();
      foreach ($durations as $duration)
      {
          if(!in_array($duration['class'], $treated))
          {
              $currentClass = array();
              $clname = $duration['class'];
              $treated[] = $clname;
              $currentClass['name'] = $clname;
              $filter = function ($element) use ($clname) { return ($element['class'] == $clname); }; 
              $currentClass['errors'] = array_filter($errors, $filter); 
              $currentClass['excepts'] = array_filter($excepts, $filter); 
              $currentClass['fails'] = array_filter($fails, $filter);
              $currentClass['durations'] = array_filter($durations, $filter); 
              $classes[] = $currentClass; 
          }
      }
      $testsuite = null;
      $testcase = null;
      
      foreach ($classes as $classe)
      {
          $cl         = new \ReflectionClass($classe['name']);
          $clname     = $cl->getShortName(); 
          $package    = str_replace('\\','.',$cl->getNamespaceName());
          $testsuite  = $document->createElement('testsuite'); 
          $testsuite->setAttribute('name',$clname);
          $testsuite->setAttribute('package',$package);
          $testsuite->setAttribute('tests',sizeof($classe['durations']));
          $testsuite->setAttribute('failures',sizeof($classe['fails']));
          $testsuite->setAttribute('errors',(sizeof($classe['excepts'])+sizeof($classe['errors'])));
          $time        = 0;
          foreach ($classe['durations'] as $duration)
          {
              $time += $duration['value'];
              $methName = $duration['method'];
              $testcase = $document->createElement('testcase');
              $testcase->setAttribute('name', $methName);
              $testcase->setAttribute('time', $duration['value']);
              $testcase->setAttribute('classname', $clname);
              $filter = function ($element) use ($methName) { return ($element['method'] == $methName); };  
              $failures = array_filter($classe['fails'], $filter);
              foreach ($failures as $failure)
              {
                  $xfail = $document->createElement('failure',$failure['fail']);
                  $xfail->setAttribute('type','Assertion Fail');
                  $xfail->setAttribute('message',$failure['asserter']);
                  $testcase->appendChild($xfail);
              }
              $mExcepts = array_filter($classe['excepts'], $filter);
              foreach ($mExcepts as $except)
              {
                  $xexcept = $document->createElement('error');
                  $xexcept->setAttribute('type','Exception');
                  $cont = $document->createCDATASection($except['value']);
                  $xexcept->appendChild($cont);
                  $testcase->appendChild($xexcept);
              }
              $testsuite->appendChild($testcase);
          }
          
          foreach ($classe['errors'] as $cError)
          {
              $methName = $cError['method'];
              $testcase = $document->createElement('testcase');
              $testcase->setAttribute('name', $methName);
              $testcase->setAttribute('time', '0');
              $testcase->setAttribute('classname', $clname);

              $xerror = $document->createElement('error');
              $xerror->setAttribute('type',$cError['type']);
              $cont = $document->createCDATASection($cError['message']);
              $xerror->appendChild($cont);
              $testcase->appendChild($xerror);
              $testsuite->appendChild($testcase);
          }
          
          $testsuite->setAttribute('time',$time);
          $root->appendChild($testsuite);
      }
      
      return $document->saveXML();
      
	}
}

?>
