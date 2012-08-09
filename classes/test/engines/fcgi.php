<?php

namespace mageekguy\atoum\test\engines;

use
	mageekguy\atoum,
	mageekguy\atoum\test,
	mageekguy\atoum\test\engines
;

class fcgi extends test\engine
{
	private $stream = null;

	public function isRunning()
	{
		return ($this->stream !== null && $this->stream->waitResponses() === true);
	}

	public function isAsynchronous()
	{
		return true;
	}

	public function run(atoum\test $test)
	{
		$this->stream = $test->getFastCgiStream();

		$currentTestMethod = $test->getCurrentMethod();

		if ($currentTestMethod !== null)
		{
			$this->stream->write(new engines\fcgi\request($test, $this->stream));
		}

		return $this;
	}

	public function getScore()
	{
		$score = null;

		if ($this->isRunning() === true)
		{
			$responses = $this->stream->read();

			if (sizeof($responses) > 0)
			{
				$score = $this->factory['mageekguy\atoum\score']();

				foreach ($responses as $response)
				{
					$request = $response->getRequest();
					$stdOut = $response->getHttpBody();
					$stdErr = $response->getStderr();

					$methodScore = @unserialize($stdOut);

					if ($methodScore instanceof atoum\score === false)
					{
						$methodScore = $this
							->factory['mageekguy\atoum\score']()
							->addUncompletedMethod($request->getLocalTestPath(), $request->getTestClass(), $request->getTestMethod(), $response->getExitCode(), $stdOut)
						;
					}

					if ($stdErr !== '')
					{
						if (preg_match_all('/([^:]+): (.+) in (.+) on line ([0-9]+)/', trim($stdErr), $errors, PREG_SET_ORDER) === 0)
						{
							$methodScore->addError($request->getLocalTestPath(), $request->getTestClass(), $request->getTestMethod(), null, 'UNKNOWN', $stdErr);
						}
						else foreach ($errors as $error)
						{
							$methodScore->addError($request->getLocalTestPath(), $request->getTestClass(), $request->getTestMethod(), null, $error[1], $error[2], $error[3], $error[4]);
						}
					}

					$score->merge($methodScore);
				}
			}
		}

		return $score;
	}
}
