<?php

declare(strict_types=1);

namespace App\Actions;

class CreateInvoice
{
    public function __invoke()
    {
        view('create', [
            'prefillDate' => (new \DateTime())->format('Y-m-d'),
            'prefillDueDate' => (new \DateTime())->format('Y-m-t'),
        ]);
    }
}
