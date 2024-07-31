<?php

declare(strict_types=1);

namespace App\Models;

use JsonSerializable;

class Invoice implements JsonSerializable
{
    public function __construct(
        private string $series,
        private string $seriesNumber,
        private \DateTime $date,
        private \DateTime $dateDue,
        private InvoiceParty $seller,
        private InvoiceParty $buyer,
        private array $items,
        private ?string $notes = null,
    ) {}

    public function getSeries(): string
    {
        return $this->series;
    }

    public function setSeries(string $series): self
    {
        $this->series = $series;

        return $this;
    }

    public function getSeriesNumber(): string
    {
        return $this->seriesNumber;
    }

    public function setSeriesNumber(string $seriesNumber): self
    {
        $this->seriesNumber = $seriesNumber;

        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDateDue(): \DateTime
    {
        return $this->dateDue;
    }

    public function setDateDue(\DateTime $dateDue): self
    {
        $this->dateDue = $dateDue;

        return $this;
    }

    public function getSeller(): InvoiceParty
    {
        return $this->seller;
    }

    public function setSeller(InvoiceParty $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getBuyer(): InvoiceParty
    {
        return $this->buyer;
    }

    public function setBuyer(InvoiceParty $buyer): self
    {
        $this->buyer = $buyer;

        return $this;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'series' => $this->series,
            'seriesNumber' => $this->seriesNumber,
            'date' => $this->date,
            'dateDue' => $this->dateDue,
            'seller' => $this->seller,
            'buyer' => $this->buyer,
            'items' => $this->items,
            'notes' => $this->notes,
        ];
    }
}
