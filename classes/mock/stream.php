<?php

namespace mageekguy\atoum\mock;

use
	mageekguy\atoum\exceptions
;

class stream
{
	const name = 'atoum';

	public $context = null;

	protected $streamController = null;

	protected static $streams = array();

	public function __call($method, $arguments)
	{
		switch ($method)
		{
			case 'dir_opendir':
			case 'mkdir':
			case 'rename':
			case 'rmdir':
			case 'stream_metadata':
			case 'stream_open':
			case 'unlink':
			case 'url_stat':
				$this->streamController = self::get(parse_url($arguments[0], PHP_URL_HOST));
				break;
		}

		return $this->streamController->invoke($method, $arguments);
	}

	public static function get($stream)
	{
		if (in_array(self::name, stream_get_wrappers()) === false && stream_wrapper_register(self::name, __CLASS__) === false)
		{
			throw new exceptions\runtime('Unable to register ' . self::name . ' stream');
		}

		if (isset(self::$streams[$stream]) === false)
		{
			self::$streams[$stream] = new stream\controller();
		}

		return self::$streams[$stream];
	}
}

?>
