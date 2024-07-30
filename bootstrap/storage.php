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

function find_all_invoices(): array
{
    $path = __DIR__ . '/../storage/';

    $files = glob($path . '*');

    $invoices = [];
    foreach ($files as $file) {
        preg_match('/[^\/\\]]+$/', $file, $matches);
        $filename = $matches[0];
        $invoices[] = $filename;
    }
    
    return $invoices;
}

function delete_all_invoices(): void
{
    $path = __DIR__ . '/../storage/';

    $files = glob($path . '*');

    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
}
