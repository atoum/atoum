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

		if (strtolower($method) === 'stream_open')
		{
			$stream = static::getStreamFromArguments($arguments);

			$this->streamController
				->linkModeTo($stream)
				->linkContentsTo($stream)
				->linkLockTo($stream)
			;
		}

		return $this;
	}

	protected static function getController($stream)
	{
		return new file\controller($stream);
	}
}
