<?php

namespace mageekguy\atoum\mailers;

use
	mageekguy\atoum,
	mageekguy\atoum\exceptions
;

class mail extends atoum\mailer
{
	const eol = "\r\n";

	public function send($something)
	{
		if ($this->to === null)
		{
			throw new exceptions\runtime('To is undefined');
		}

		if ($this->subject === null)
		{
			throw new exceptions\runtime('Subject is undefined');
		}

		if ($this->from === null)
		{
			throw new exceptions\runtime('From is undefined');
		}

		if ($this->replyTo === null)
		{
			throw new exceptions\runtime('Reply to is undefined');
		}

		if ($this->xMailer === null)
		{
			throw new exceptions\runtime('X-mailer is undefined');
		}

		$headers = 'From: ' . $this->from . self::eol . 'Reply-To: ' . $this->replyTo . self::eol . 'X-Mailer: ' . $this->xMailer;

		if ($this->contentType !== null)
		{
			$headers .= self::eol . 'Content-Type: ' . $this->contentType[0] . '; charset="' . $this->contentType[1] . '"';
		}

		$this->adapter->mail($this->to, $this->subject, (string) $something, $headers);

		return $this;
	}
}
