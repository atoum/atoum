<?php

namespace mageekguy\atoum\mock\streams;

use
	mageekguy\atoum\mock\stream
;

class file extends stream
{
	protected function setControllerForMethod($method, array $arguments)
	{
		parent::setControllerForMethod($method, $arguments);

		switch (strtolower($method))
		{
			case 'dir_opendir':
			case 'mkdir':
			case 'rename':
			case 'rmdir':
			case 'stream_metadata':
			case 'stream_open':
			case 'unlink':
			case 'url_stat':
			case 'stat':
				$stream = static::getStreamFromArguments($arguments);

				$this->streamController
					->linkStatsTo($stream)
					->linkContentsTo($stream)
				;
		}

		return $this;
	}

	protected static function getController($stream)
	{
		return new file\controller($stream);
	}
}
