<?php

declare(strict_types=1);

namespace App\Models;

use JsonSerializable;

class Money implements JsonSerializable
{
    public function __construct(
        private int $amount,
        private int $cents,
        private string $currency = 'EUR',
    ) {}

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCents(): int
    {
        return $this->cents;
    }

    public function setCents(int $cents): self
    {
        $this->cents = $cents;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'amount' => $this->amount,
            'cents' => $this->cents,
            'currency' => $this->currency,
        ];
    }
}
