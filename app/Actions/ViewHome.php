<?php

declare(strict_types=1);

namespace App\Actions;

use App\Services\WordDocGenerator;

class ViewHome
{
    public function __invoke()
    {
        (new WordDocGenerator())->generate();

        view('home', [
            'invoices' => find_all_invoices(),
        ]);
    }
}
