<?php

namespace mageekguy\atoum\asserters;

use
	mageekguy\atoum\php,
	mageekguy\atoum\mock,
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class directory extends asserters\adapter
{
	public function setWith($value)
	{
		if ($value instanceof mock\filesystem\directory === false && is_dir((string) $value) === false)
		{
			$this->fail(sprintf($this->getLocale()->_('%s is not a directory'), $this->getTypeOf((string) $value)));
		}
		else
		{
			if ($value instanceof mock\filesystem\directory)
			{
				$adapter = $value->getStream();
			}
			else
			{
				$adapter = mock\stream::get($value);
			}

			parent::setWith($adapter);
		}

		return $this;
	}

	public function hasBeenChecked($failMessage = null)
	{
		return $this
			->call('url_stat')
			->atLeastOnce($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has not been checked'), $this->adapter))
		;
	}

	public function hasNotBeenChecked($failMessage = null)
	{
		return $this
			->call('url_stat')
			->never($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has been checked'), $this->adapter))
		;
	}

	public function hasBeenCreated($failMessage = null)
	{
		return $this
			->call('mkdir')
			->once($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has not been created'), $this->adapter))
		;
	}

	public function hasNotBeenCreated($failMessage = null)
	{
		return $this
			->call('mkdir')
			->never($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has been created'), $this->adapter))
		;
	}

	public function hasBeenDeleted($failMessage = null)
	{
		return $this
			->call('rmdir')
			->once($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has not been deleted'), $this->adapter))
		;
	}

	public function hasNotBeenDeleted($failMessage = null)
	{
		return $this
			->call('rmdir')
			->never($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has been deleted'), $this->adapter))
		;
	}

	public function hasBeenOpened($failMessage = null)
	{
		return $this
			->call('dir_opendir')
			->atLeastOnce($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has not been opened'), $this->adapter))
		;
	}

	public function hasNotBeenOpened($failMessage = null)
	{
		return $this
			->call('dir_opendir')
			->never($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has been opened'), $this->adapter))
		;
	}

	public function hasBeenRead($failMessage = null)
	{
		return $this
			->call('dir_readdir')
			->atLeastOnce($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has not been read'), $this->adapter))
		;
	}

	public function hasNotBeenRead($failMessage = null)
	{
		return $this
			->call('dir_readdir')
			->never($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has been read'), $this->adapter))
		;
	}

	public function hasBeenRewinded($failMessage = null)
	{
		return $this
			->call('dir_rewinddir')
			->atLeastOnce($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has not been rewinded'), $this->adapter))
		;
	}

	public function hasNotBeenRewinded($failMessage = null)
	{
		return $this
			->call('dir_rewinddir')
			->never($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has been rewinded'), $this->adapter))
		;
	}

	public function hasBeenClosed($failMessage = null)
	{
		return $this
			->call('dir_closedir')
			->atLeastOnce($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has not been closed'), $this->adapter))
		;
	}

	public function hasNotBeenClosed($failMessage = null)
	{
		return $this
			->call('dir_closedir')
			->never($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s has been closed'), $this->adapter))
		;
	}

	public function exists($failMessage = null)
	{
		$stream = (string) $this->adapterIsSet()->adapter;

		if (is_dir($stream) === false)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s does not exist'), $stream));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	public function doesNotExist($failMessage = null)
	{
		$stream = (string) $this->adapterIsSet()->adapter;

		if (is_dir($stream) === false)
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('directory %s exists'), $stream));
		}

		return $this;
	}
}
