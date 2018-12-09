<?php

namespace Scanner\DataTransfer;

class Money
{
    /**
     * @var int
     */
    private $value;

    public function __construct(int $value = 0)
    {
        $this->value = $value;
    }

    /**
     * @param string $value
     * @return Money
     */
    public static function fromString(string $value)
    {
        $v = intval(round(floatval($value) * 100));

        return new self($v);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return Money
     */
    public function setValue(int $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param Money $money
     * @return Money
     */
    public function modify(Money $money)
    {
        $this->value += $money->getValue();

        return $this;
    }
}
