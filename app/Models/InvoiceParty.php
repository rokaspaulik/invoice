<?php

declare(strict_types=1);

namespace App\Models;

use JsonSerializable;

class InvoiceParty implements JsonSerializable
{
    public function __construct(
        private string $name,
        private ?string $bankIbanCode,
        private ?string $address,
        private ?string $companyCode,
        private ?string $taxCode,
        private ?string $phoneNumber,
        private ?string $email,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function getCompanyCode(): ?string
    {
        return $this->companyCode;
    }

    public function getTaxCode(): ?string
    {
        return $this->taxCode;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'name' => $this->name,
            'bankIbanCode' => $this->bankIbanCode,
            'address' => $this->address,
            'companyCode' => $this->companyCode,
            'taxCode' => $this->taxCode,
            'phoneNumber' => $this->phoneNumber,
            'email' => $this->email,
        ];
    }
}
