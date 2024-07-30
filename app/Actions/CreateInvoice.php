<?php

declare(strict_types=1);

namespace App\Actions;

class CreateInvoice
{
    public function __invoke()
    {
        view('create');
    }
}
