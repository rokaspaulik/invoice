<?php

declare(strict_types=1);

namespace App\Actions;

class DeleteInvoices
{
    public function __invoke()
    {
        delete_all_invoices();
        http_redirect('/');
    }
}
