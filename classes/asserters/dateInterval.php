<?php

namespace mageekguy\atoum\asserters;
use
	mageekguy\atoum\asserters,
	mageekguy\atoum\exceptions
;

class dateInterval extends asserters\object
{
	public function setWith($value, $checkType = true)
	{
		parent::setWith($value, false);

		if ($checkType === true)
		{
			if (self::isDateInterval($this->value) === false)
			{
				$this->fail(sprintf($this->getLocale()->_('%s is not an instance of \\dateInterval'), $this));
			}
			else
			{
				$this->pass();
			}
		}

		return $this;
	}
	
	public function isGreaterThan(\dateInterval $interval,$failMessage = null)
	{
		if($this->greaterThan($interval))
		{
			$this->pass();
		}
                else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('This interval is not longer than %s years %s months %s days %s hours %s minutes %s seconds'),
				$interval->y, $interval->m, $interval->d ,$interval->h, $interval->i, $interval->s));
		}
                
		return $this;
	}
	
	public function isLessThan(\dateInterval $interval,$failMessage = null)
	{
		if($this->lowerThan($interval))
                {
			$this->pass();
		}
                else
                {
		$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('This interval is not shorter than %s years %s months %s days %s hours %s minutes %s seconds'),
				$interval->y, $interval->m, $interval->d, $interval->h, $interval->i, $interval->s));
		}
                
		return $this;
	}
        
	public function isAsLongAs(\dateInterval $interval,$failMessage = null)
	{                
		if($this->equals($interval))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('This interval is not equal to %s years %s months %s days %s hours %s minutes %s seconds'),
				$interval->y, $interval->m, $interval->d,$interval->h,$interval->i,$interval->s));
			
		}
                
		return $this;
	}
        
	public function isInRange(\dateInterval $low,\dateInterval $high,$failMessage = null)
	{
		$value = $this->valueIsSet()->value;
                
		if(($this->equals($low) || $this->greaterThan($low)) && ($this->equals($high) || $this->lowerThan($high)))
		{
			$this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->getLocale()->_('This interval is not between %s years %s months %s days %s hours %s minutes %s seconds and %s years %s months %s days %s hours %s minutes %s seconds'),
				$low->y, $low->m, $low->d,$low->h,$low->i,$low->s,
				$high->y, $high->m, $high->d,$high->h,$high->i,$high->s));
			
		}
                
		return $this;
	}
	
	protected static function isDateInterval($value)
	{
		return ($value instanceof \dateInterval);
	}
        
	protected function valueIsSet($message = 'Instance of \\dateInterval is undefined')
	{
		if (self::isDateInterval(parent::valueIsSet($message)->value) === false)
		{
			throw new exceptions\logic($message);
		}

		return $this;
	}
        
	private function lowerThan(\dateInterval $interval)
	{
		$value = $this->valueIsSet()->value;
                
		foreach($value as $attr => $val)
		{
			if($val === $interval->$attr || $attr === 'days')
			{
				continue;
			}
			else if($val < $interval->$attr)
			{
				return true;
			}
			else if($val > $interval->$attr)
			{
				return false;
			}
			
		}

	}

	private function greaterThan(\dateInterval $interval)
	{
		$value = $this->valueIsSet()->value;
		
		foreach($value as $attr => $val)
		{
			if($val === $interval->$attr || $attr === 'days')
			{
				continue;
			}
			else if($val > $interval->$attr)
			{
				return true;
			}
			else if($val < $interval->$attr)
			{
				return false;
			}
			
		}

	}
        
	private function equals(\dateInterval $interval)
	{
		$value = $this->valueIsSet()->value;
                
		return $value->y === $interval->y && $value->m === $interval->m && $value->d === $interval->d
				&& $value->h === $interval->h && $value->i === $interval->i && $value->s === $interval->s ;
	}
}
