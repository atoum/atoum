<?php

namespace mageekguy\atoum\test\annotations;

use mageekguy\atoum;

class extractor extends atoum\annotations\extractor
{
	public function extract($comments)
	{
		$this->reset();

		$annotations = parent::extract($comments)->getAnnotations();

		$this->reset();

		foreach ($annotations as $annotation => $value)
		{
			switch (strtolower($annotation))
			{
				case 'ignore':
					$this->annotations['ignore'] = strcasecmp($value, 'on') == 0;
					break;

				case 'tags':
					$this->annotations['tags'] = array_values(array_unique(preg_split('/\s+/', $value)));
					break;

				case 'dataprovider':
					$this->annotations['dataProvider'] = $value;
			}
		}

		return $this;
	}

	public function setTest(atoum\test $test, $comments)
	{
		foreach ($this->extract($comments) as $annotation => $value)
		{
			switch ($annotation)
			{
				case 'ignore':
					$test->ignore($value);
					break;

				case 'tags':
					$test->setTags($value);
					break;
			}
		}

		return $this;
	}

	public function setTestMethod(atoum\test $test, $method, $comments)
	{
		foreach ($this->extract($comments) as $annotation => $value)
		{
			switch ($annotation)
			{
				case 'ignore':
					$test->ignoreMethod($method, $value);
					break;

				case 'tags':
					$test->setMethodTags($method, $value);
					break;

				case 'dataProvider':
					$test->setDataProvider($method, $value);
					break;
			}
		}

		return $this;
	}
}

?>
