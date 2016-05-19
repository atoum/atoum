<?php

namespace mageekguy\atoum\asserters;

class mysqlDateTime extends dateTime
{
	const mysqlDateTimeFormat = 'Y-m-d H:i:s';

	public function setWith($value, $checkType = true)
	{
		$phpDate = \dateTime::createFromFormat(self::mysqlDateTimeFormat, $value);

		if ($phpDate !== false)
		{
			parent::setWith($phpDate, $checkType);
		}
		else
		{
			parent::setWith($value, false);

			if ($checkType === true)
			{
				$this->fail($this->_('%s is not in format Y-m-d H:i:s', $this));
			}
		}

		return $this;
	}

	public function getValue()
	{
		$value = parent::getValue();

		return ($value instanceof \dateTime === false ? $value : $value->format(self::mysqlDateTimeFormat));
	}
}
