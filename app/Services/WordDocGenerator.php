<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\TextAlignment;

class WordDocGenerator
{
    private const FILE_PATH = 'storage/output/';
    private const TABLE_BS_CELL_WIDTH = 4000;
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
        $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText('Pirkėjas', $font);
        $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText('Pardavėjas', $font);

        $font = ['size' => 9];
        for ($row = 1; $row <= self::TABLE_BS_ROWS; ++$row) {
            $table->addRow();

            for ($col = 1; $col <= self::TABLE_BS_COLS; ++$col) {
                if ($col % 2) {
                    // Add seller info
                    $value = !empty($sellerInfo) ? array_pop($sellerInfo) : '';
                    $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText($value, $font);
                    continue;
                }
                // Add buyer info
                $value = !empty($buyerInfo) ? array_pop($buyerInfo) : '';
                $table->addCell(self::TABLE_BS_CELL_WIDTH)->addText($value, $font);
            }
        }

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
