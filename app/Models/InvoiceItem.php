<?php

declare(strict_types=1);

namespace App\Models;

use JsonSerializable;

class InvoiceItem implements JsonSerializable
{
    public function __construct(
        private string $name,
        private int $amount,
        private string $unit,
        private Money $money,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getMoney(): Money
    {
        return $this->money;
    }

    public function setMoney(Money $money): self
    {
        $this->money = $money;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name,
            'amount' => $this->amount,
            'unit' => $this->unit,
            'money' => $this->money,
        ];
    }
}
