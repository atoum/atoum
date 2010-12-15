<?
namespace mageekguy\atoum\report\fields\runner\duration;

use \mageekguy\atoum;
use \mageekguy\atoum\report;

class string extends report\fields\runner\duration
{
	const titlePrompt = '> ';

	public function __toString()
	{
		$string = self::titlePrompt;

		if ($this->value === null)
		{
			$string .= $this->locale->_('Running duration: unknown.');
		}
		else
		{
			$string .= sprintf($this->locale->__('Running duration: %4.2f second.', 'Running duration: %4.2f seconds.', $this->value), $this->value);
		}

		$string .= PHP_EOL;

		return $string;
	}
}

?>