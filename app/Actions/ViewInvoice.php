<?php

declare(strict_types=1);

namespace App\Actions;

use App\Services\WordDocGenerator;

class ViewInvoice
{
    public function __invoke()
    {
        $invoice = load_invoice($_GET['invoice']);

        $generator = new WordDocGenerator($invoice);
        $generator->generate();

        echo "<pre>";
        print_r($invoice);
        echo "</pre>";

        view('view', [
            'invoice' => $invoice
        ]);
    }
}
