<?php

use App\Models\Invoice;
use App\Models\InvoiceParty;

function store_invoice(Invoice $invoice): void
{
    $json = json_encode($invoice);
    $filename = trim($invoice->getSeries()) . '_' . trim($invoice->getSeriesNumber() . '-' . time());
    $path = __DIR__ . '/../storage/' . $filename . '.json';

    file_put_contents($path, $json);
}

function load_invoice(string $filename): Invoice
{
    $path = __DIR__ . '/../storage/' . $filename . '.json';
    $file = file_get_contents($path);

    $data = json_decode($file, true);
    $buyerData = $data['buyer'];
    $sellerData = $data['seller'];
    
    $buyer = new InvoiceParty(
        name: $buyerData['name'],
        bankIbanCode: $buyerData['bankIbanCode'],
        address: $buyerData['address'],
        companyCode: $buyerData['companyCode'],
        taxCode: $buyerData['taxCode'],
        phoneNumber: $buyerData['phoneNumber'],
        email: $buyerData['email'],
    );

    $seller = new InvoiceParty(
        name: $sellerData['name'],
        bankIbanCode: $sellerData['bankIbanCode'],
        address: $sellerData['address'],
        companyCode: $sellerData['companyCode'],
        taxCode: $sellerData['taxCode'],
        phoneNumber: $sellerData['phoneNumber'],
        email: $sellerData['email'],
    );

    $invoice = new Invoice(
        series: $data['series'],
        seriesNumber: $data['seriesNumber'],
        date: new \DateTime($data['date']['date']),
        dateDue: new \DateTime($data['dateDue']['date']),
        seller: $seller,
        buyer: $buyer,
        items: $data['items'],
        notes: $data['notes'],
    );
    
    return $invoice;
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
