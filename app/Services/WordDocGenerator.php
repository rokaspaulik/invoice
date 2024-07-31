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

        // Seller section
        $font = ['bold' => true, 'size' => 9];
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText('Pirkėjas', $font);

        foreach ($sellerInfo as $info) {
            if (empty($info) || !is_string($info)) {
                continue;
            }

            $font = ['size' => 9];
            $section = $word->addSection(['breakType' => 'continuous']);
            $section->addText($info, $font);
        }

        // Buyer section
        $font = ['bold' => true, 'size' => 9];
        $section = $word->addSection(['breakType' => 'continuous']);
        $section->addText('Pardavėjas', $font);

        $section = $word->addSection(['breakType' => 'continuous', 'colsNum' => 2]);
        foreach ($buyerInfo as $info) {
            if (empty($info) || !is_string($info)) {
                continue;
            }

            $font = ['size' => 9];
            $section->addText($info, $font);
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
