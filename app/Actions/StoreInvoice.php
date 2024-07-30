<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Invoice;
use App\Models\InvoiceParty;

class StoreInvoice
{
    public function __invoke()
    {
        $buyer = new InvoiceParty(
            name: $_POST['buyer_name'],
            bankIbanCode: empty($_POST['buyer_bank_iban']) ? null : $_POST['buyer_bank_iban'],
            address: empty($_POST['buyer_address']) ? null : $_POST['buyer_address'],
            companyCode: empty($_POST['buyer_company_code']) ? null : $_POST['buyer_company_code'],
            taxCode: empty($_POST['buyer_tax_code']) ? null : $_POST['buyer_tax_code'],
            phoneNumber: empty($_POST['buyer_phone_number']) ? null : $_POST['buyer_phone_number'],
            email: empty($_POST['buyer_email']) ? null : $_POST['buyer_email'],
        );

        $seller = new InvoiceParty(
            name: $_POST['seller_name'],
            bankIbanCode: empty($_POST['seller_bank_iban']) ? null : $_POST['seller_bank_iban'],
            address: empty($_POST['seller_address']) ? null : $_POST['seller_address'],
            companyCode: empty($_POST['seller_company_code']) ? null : $_POST['seller_company_code'],
            taxCode: empty($_POST['seller_tax_code']) ? null : $_POST['seller_tax_code'],
            phoneNumber: empty($_POST['seller_phone_number']) ? null : $_POST['seller_phone_number'],
            email: empty($_POST['seller_email']) ? null : $_POST['seller_email'],
        );

        $invoice = new Invoice(
            series: $_POST['series'],
            seriesNumber: $_POST['series_number'],
            date: new \DateTime($_POST['date']),
            dateDue: new \DateTime($_POST['date_due']),
            seller: $seller,
            buyer: $buyer,
            items: [],
        );

        store_invoice($invoice);
        redirect('/');
    }
}
