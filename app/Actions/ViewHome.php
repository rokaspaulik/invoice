<?php

declare(strict_types=1);

namespace App\Actions;

class ViewHome
{
    public function __invoke()
    {
        view('home', [
            'invoices' => find_all_invoices(),
        ]);
    }
}
