<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier\image;

use
	mageekguy\atoum,
	mageekguy\atoum\report\fields\runner\result\notifier\image
;

class libnotify extends image
{
	public function getCommand()
	{
		return 'notify-send -i %3$s %1$s %2$s';
	}
}
