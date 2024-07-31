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
    private const FILE_PATH = 'storage/output/';
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
        $sellerInfo = $this->invoice->getSeller()->getInfo();
        $buyerInfo = $this->invoice->getBuyer()->getInfo();

        // Heading section
        $font = ['bold' => true, 'size' => 18];
        $paragraph = ['alignment' => TextAlignment::CENTER, 'spaceAfter' => 240];
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText('SĄSKAITA FAKTŪRA', $font, $paragraph);

        // Series section
        $font = ['size' => 9];
        $paragraph = ['alignment' => TextAlignment::CENTER];
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText(sprintf('Serija %s Nr. %s', $this->invoice->getSeries(), $this->invoice->getSeriesNumber()), $font, $paragraph);

        // Date section
        $font = ['size' => 9];
        $paragraph = ['alignment' => TextAlignment::CENTER];
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText(sprintf('Sąskaitos data %s', $this->invoice->getDate()->format('Y-m-d'), $this->invoice->getSeriesNumber()), $font, $paragraph);

        // Date due section
        $font = ['size' => 9];
        $paragraph = ['alignment' => TextAlignment::CENTER, 'spaceAfter' => 400];
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText(sprintf('Apmokėti iki %s', $this->invoice->getDateDue()->format('Y-m-d'), $this->invoice->getSeriesNumber()), $font, $paragraph);

        // Buyer and seller table section
        $table = $section->addTable();
        $table->addRow();

        $font = ['bold' => true, 'size' => 9];
        $section = $word->addSection(['breakType' => 'continuous']);
        $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText('Pardavėjas', $font);
        $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText('Pirkėjas', $font);

        $font = ['size' => 9];
        for ($row = 1; $row <= self::TABLE_BS_ROWS; ++$row) {
            $table->addRow();

            for ($col = 1; $col <= self::TABLE_BS_COLS; ++$col) {
                if ($col % 2) {
                    // Add seller info
                    $value = !empty($sellerInfo) ? array_shift($sellerInfo) : '';
                    $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText($value, $font);
                    continue;
                }
                // Add buyer info
                $value = !empty($buyerInfo) ? array_shift($buyerInfo) : '';
                $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText($value, $font);
            }
        }

        // Invoice items table
        $table = $section->addTable();
        $table->addRow();

        $font = ['bold' => true, 'size' => 9];
        $section = $word->addSection(['breakType' => 'continuous']);
        $table->addCell(2000)->addText('Pavadinimas', $font);
        $table->addCell(2000)->addText('Kiekis', $font);
        $table->addCell(2000)->addText('Matas', $font);
        $table->addCell(2000)->addText('Kaina', $font);
        $table->addCell(2000)->addText('Iš viso', $font);

        $font = ['size' => 9];
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

            $table->addCell(2000)->addText($invoiceItem->getName(), $font);
            $table->addCell(2000)->addText($invoiceItem->getAmount(), $font);
            $table->addCell(2000)->addText($invoiceItem->getUnit(), $font);
            $table->addCell(2000)->addText(MoneyHandler::format($invoiceItem->getMoney()), $font);
            $table->addCell(2000)->addText(MoneyHandler::format($itemTotalMoneyObject), $font);
        }

        // Total section
        $invoiceCents = MoneyHandler::calculateInvoiceTotalInCents($this->invoice);
        $money = MoneyHandler::fromCents($invoiceCents);
        $table->addRow();
        $table->addCell(8000)->addText('Bendra suma', ['bold' => true, 'size' => 9], ['spaceBefore' => 400, 'alignment' => Jc::END]);
        $table->addCell(1000)->addText(MoneyHandler::format($money), ['size' => 9], ['spaceBefore' => 400]);

        // Money words section
        $moneyWords = MoneyHandler::moneyToWords($money);
        $font = ['size' => 9];
        $paragraph = ['spaceBefore' => 1000, 'spaceAfter' => 500];
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText(sprintf('Suma žodžiais: %s', $moneyWords), $font, $paragraph);

        // Seller credentials section
        $font = ['size' => 9];
        $paragraph = ['spaceAfter' => 500];
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText(sprintf('Sąskaitą išrašė: %s', $this->invoice->getSeller()->getName()), $font, $paragraph);

        // Buyer credentials section
        $font = ['size' => 9];
        $paragraph = ['spaceAfter' => 500];
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText('Sąskaitą priėmė: _______________________________', $font, $paragraph);

        // Notes section
        $section->addText('Pastabos', ['size' => 9, 'bold' => true], ['spaceAfter' => 100]);
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText($this->invoice->getNotes(), ['size' => 9]);

        $this->exportAsWord($word);
    }

    private function exportAsWord(PhpWord $word): void
    {
        $path = self::FILE_PATH;
        $filename = 'output';
        $extension = '.docx';

        (IOFactory::createWriter($word, 'Word2007'))
            ->save(sprintf('%s%s%s', $path, $filename, $extension));
    }
}
