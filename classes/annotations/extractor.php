<?php

namespace mageekguy\atoum\annotations;

class extractor implements \iteratorAggregate
{
	protected $annotations = array();

	public function __construct($comments = null)
	{
		if ($comments !== null)
		{
			$this->extract($comments);
		}
	}

	public function reset()
	{
		$this->annotations = array();

		return $this;
	}

	public function extract($comments)
	{
		$comments = trim((string) $comments);

		if (substr($comments, 0, 3) == '/**' && substr($comments, -2) == '*/')
		{
			foreach (explode("\n", trim(trim($comments, '/*'))) as $comment)
			{
				$comment = trim(trim(trim($comment), '*/'));

				if (substr($comment, 0, 1) == '@')
				{
					$comment = preg_split("/\s+/", $comment);

					$sizeofComment = sizeof($comment);

					if ($sizeofComment >= 2)
					{
						$this->annotations[substr($comment[0], 1)] = $sizeofComment == 2 ? $comment[1] : join(' ', array_slice($comment, 1));
					}
				}
			}
		}

		return $this;
	}

	public function getIterator()
	{
		return new \arrayIterator($this->annotations);
	}

	public function getAnnotations()
	{
		return $this->annotations;
	}
}

?>
