<?php

declare(strict_types=1);

namespace App\Actions;

class ViewHome
{
    public function __invoke()
    {
        // $invoice = load_invoice('dsaasd_sdasaddassdaasd-1722364493');
        // echo "<pre>";
        // echo print_r($invoice);
        // echo "</pre>";

        view('home', [
            'invoices' => find_all_invoices(),
        ]);
    }
}
