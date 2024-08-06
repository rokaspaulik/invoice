<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Money;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TextAlignment;

class InvoiceDocumentGenerator
{
    private const TABLE_BS_CELL_WIDTH = 5000;
    private const TABLE_BS_ROWS = 10;
    private const TABLE_BS_COLS = 2;

    public function __construct(
        private Invoice $invoice,
    ) {
    }

    public function generate(): void
    {
        $word = new PhpWord();

        $this->generateHeading($word)
            ->generateSeries($word)
            ->generateDate($word)
            ->generateBuyerSellerInfo($word)
            ->generateInvoiceItemsTable($word)
            ->generateBuyerSellerCredentials($word)
            ->generateNotes($word);

        $this->exportAsWord($word);
    }

    private function generateHeading(PhpWord $word): self
    {
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText('SĄSKAITA FAKTŪRA', ['bold' => true, 'size' => 18], ['alignment' => TextAlignment::CENTER, 'spaceAfter' => 240]);

        return $this;
    }

    private function generateSeries(PhpWord $word): self
    {
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText(sprintf('Serija %s Nr. %s', $this->invoice->getSeries(), $this->invoice->getSeriesNumber()), ['size' => 9], ['alignment' => TextAlignment::CENTER]);

        return $this;
    }

    private function generateDate(PhpWord $word): self
    {
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText(sprintf('Sąskaitos data %s', $this->invoice->getDate()->format('Y-m-d'), $this->invoice->getSeriesNumber()), ['size' => 9], ['alignment' => TextAlignment::CENTER]);

        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText(sprintf('Apmokėti iki %s', $this->invoice->getDateDue()->format('Y-m-d'), $this->invoice->getSeriesNumber()), ['size' => 9], ['alignment' => TextAlignment::CENTER, 'spaceAfter' => 400]);

        return $this;
    }

    private function generateBuyerSellerInfo(PhpWord $word): self
    {
        $sellerInfo = $this->invoice->getSeller()->getInfo();
        $buyerInfo = $this->invoice->getBuyer()->getInfo();

        $section = $word->addSection(['breakType' => 'continuous']);
        $table = $section->addTable();
        $table->addRow();

        $section = $word->addSection(['breakType' => 'continuous']);
        $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText('Pardavėjas', ['bold' => true, 'size' => 9]);
        $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText('Pirkėjas', ['bold' => true, 'size' => 9]);

        for ($row = 1; $row <= self::TABLE_BS_ROWS; ++$row) {
            $table->addRow();

            for ($col = 1; $col <= self::TABLE_BS_COLS; ++$col) {
                if ($col % 2) {
                    $value = !empty($sellerInfo) ? array_shift($sellerInfo) : '';
                    $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText($value, ['size' => 9]);
                    continue;
                }
                $value = !empty($buyerInfo) ? array_shift($buyerInfo) : '';
                $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText($value, ['size' => 9]);
            }
        }

        return $this;
    }

    private function generateInvoiceItemsTable(PhpWord $word): self
    {
        $section = $word->addSection(['breakType' => 'continuous']);
        $table = $section->addTable();
        $table->addRow();

        $section = $word->addSection(['breakType' => 'continuous']);
        $table->addCell(4000)->addText('Pavadinimas', ['bold' => true, 'size' => 9]);
        $table->addCell(1000)->addText('Kiekis', ['bold' => true, 'size' => 9]);
        $table->addCell(1000)->addText('Matas', ['bold' => true, 'size' => 9]);
        $table->addCell(2000)->addText('Kaina', ['bold' => true, 'size' => 9]);
        $table->addCell(2000)->addText('Iš viso', ['bold' => true, 'size' => 9]);

        foreach ($this->invoice->getItems() as $item) {
            $table->addRow();

            $invoiceItem = new InvoiceItem(
                name: $item['name'],
                amount: (int) $item['amount'],
                unit: $item['unit'],
                money: new Money(
                    amount: $item['money']['amount'],
                    cents: $item['money']['cents'],
                    currency: $item['money']['currency'],
                ),
            );

            $itemTotalMoneyObject = MoneyHandler::calculateInvoiceItemTotal($invoiceItem);

            $table->addCell(2000)->addText($invoiceItem->getName(), ['size' => 9]);
            $table->addCell(2000)->addText($invoiceItem->getAmount(), ['size' => 9]);
            $table->addCell(2000)->addText($invoiceItem->getUnit(), ['size' => 9]);
            $table->addCell(2000)->addText(MoneyHandler::format($invoiceItem->getMoney()), ['size' => 9]);
            $table->addCell(2000)->addText(MoneyHandler::format($itemTotalMoneyObject), ['size' => 9]);
        }

        $invoiceCents = MoneyHandler::calculateInvoiceTotalInCents($this->invoice);
        $money = MoneyHandler::fromCents($invoiceCents);
        $table->addRow();
        $table->addCell(8000)->addText('Bendra suma', ['bold' => true, 'size' => 9], ['spaceBefore' => 400, 'alignment' => Jc::END]);
        $table->addCell(1000)->addText(MoneyHandler::format($money), ['size' => 9], ['spaceBefore' => 400]);

        $moneyWords = MoneyHandler::moneyToWords($money);
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText(sprintf('Suma žodžiais: %s', $moneyWords), ['size' => 9], ['spaceBefore' => 1000, 'spaceAfter' => 500]);

        return $this;
    }

    private function generateBuyerSellerCredentials(PhpWord $word): self
    {
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText(sprintf('Sąskaitą išrašė: %s', $this->invoice->getSeller()->getName()), ['size' => 9], ['spaceAfter' => 500]);

        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText('Sąskaitą priėmė: _______________________________', ['size' => 9], ['spaceAfter' => 500]);

        return $this;
    }

    private function generateNotes(PhpWord $word): self
    {
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText('Pastabos', ['size' => 9, 'bold' => true], ['spaceAfter' => 100]);

        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText($this->invoice->getNotes(), ['size' => 9]);

        return $this;
    }

    private function exportAsWord(PhpWord $word): void
    {
        $path = 'storage/output/';
        $filename = 'output';
        $extension = '.docx';

        (IOFactory::createWriter($word, 'Word2007'))->save(sprintf('%s%s%s', $path, $filename, $extension));
    }
}
