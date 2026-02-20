<?php


namespace WHMCS\Utility;
class Bitmask
{
    protected $mask;
    const DEFAULT = 0;
    const BIT_COUNT = -1;
    public function __construct(int $mask)
    {
        $this->mask = $mask;
    }
    public static function make()
    {
        return new static(static::DEFAULT);
    }
    public function mask() : int
    {
        return $this->mask;
    }
    public function default() : int
    {
        $previous = $this->mask;
        $this->mask = static::DEFAULT;
        return $previous;
    }
    public function has($bits) : int
    {
        return ($this->mask & $bits) === $bits;
    }
    public function hasnt($bits) : int
    {
        return !$this->has($bits);
    }
    public function set($bits) : int
    {
        $hadnt = $this->hasnt($bits);
        $this->mask |= $bits;
        return $hadnt;
    }
    public function unset($bits) : int
    {
        $had = $this->has($bits);
        $this->mask &= ~$bits;
        return $had;
    }
    public function flip($bits) : int
    {
        $had = $this->has($bits);
        $this->mask ^= $bits;
        return $had;
    }
    public function as($bits, $reference) : int
    {
        $had = $this->has($bits);
        if($reference) {
            $this->set($bits);
        } else {
            $this->unset($bits);
        }
        return $had;
    }
    public function all()
    {
        $this->assertBitCountKnown();
        $previous = new static($this->mask);
        $this->mask = pow(2, static::BIT_COUNT) - 1;
        return $previous;
    }
    protected function assertBitCountKnown() : void
    {
        if(is_int(static::BIT_COUNT) && 0 < static::BIT_COUNT) {
            return NULL;
        }
        throw new \BadMethodCallException(sprintf("%s::BIT_COUNT is unknown or invalid", static::class));
    }
    public function __toString()
    {
        return str_pad(decbin($this->mask), static::BIT_COUNT, "0", STR_PAD_LEFT);
    }
}

?>