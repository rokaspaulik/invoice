<?php

declare(strict_types=1);

namespace App\Actions;

class ViewHome
{
    public function __invoke()
    {
        find_all_invoices();

        view('home', [
            'testing' => 'testing data working',
        ]);
    }
}
