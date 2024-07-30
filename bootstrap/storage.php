<?php

use App\Models\Invoice;

function store_invoice(Invoice $invoice): void
{
    $json = json_encode($invoice);
    $filename = trim($invoice->getSeries()) . '_' . trim($invoice->getSeriesNumber() . '-' . time());
    $path = __DIR__ . '/../storage/' . $filename . '.json';

    file_put_contents($path, $json);
}

function load_invoice(string $filename): array
{
    $path = __DIR__ . '/../storage/' . $filename . '.json';
    $file = file_get_contents($path);
    $data = json_decode($file, true);
    
    return $data;
}
