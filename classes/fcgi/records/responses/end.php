<?php

namespace mageekguy\atoum\fcgi\records\responses;

use
	mageekguy\atoum\fcgi\record,
	mageekguy\atoum\fcgi\records
;

class end extends records\response
{
	const type = '3';
	const requestComplete = '0';
	const serverCanNotMultiplexConnection = '1';
	const serverIsOverloaded = '2';
	const serverDoesNotKnowRole = '3';

	public function __construct($requestId, $contentData)
	{
		if (strlen($contentData) != 8)
		{
			throw new record\exception('Content data are invalid');
		}

		parent::__construct(self::type, $requestId);

		switch (ord($contentData[4]))
		{
			case self::serverCanNotMultiplexConnection:
				throw new record\exception('Server can not multiplex connection');

			case self::serverIsOverloaded:
				throw new record\exception('Server is overloaded');

			case self::serverDoesNotKnowRole:
				throw new record\exception('Server does not know the role');
		}
	}
}
