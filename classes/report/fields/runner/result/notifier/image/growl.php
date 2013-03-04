<?php

namespace mageekguy\atoum\report\fields\runner\result\notifier\image;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions\logic,
	mageekguy\atoum\report\fields\runner\result\notifier\image
;

class growl extends image
{
	protected function getCommand()
	{
		return 'growlnotify --title %s --name atoum --message %s --image %s';
	}
}
