<?php

declare(strict_types=1);

namespace App\Actions;

class ViewInvoice
{
    public function __invoke()
    {
        $invoice = load_invoice($_GET['invoice']);

        echo "<pre>";
        print_r($invoice);
        echo "</pre>";

        view('view', [
            'invoice' => $invoice
        ]);
    }
}
