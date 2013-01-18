<?php

namespace mageekguy\atoum\mock\streams;

use
	mageekguy\atoum\mock\stream
;

class file extends stream
{
	const defaultProtocol = 'atoumfile';

	protected function setControllerForMethod($method, array $arguments)
	{
		parent::setControllerForMethod($method, $arguments);

		switch (strtolower($method))
		{
			case 'stream_open':
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
