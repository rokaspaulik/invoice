<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Invoice;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

class WordDocGenerator
{
    private const FILE_PATH = 'storage/output/';

    // public function generate(Invoice $invoice): mixed
    public function generate(): void
    {
        $word = new PhpWord();

        $section = $word->addSection();
        $section->addText('Testing some text');

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
