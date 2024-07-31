<?php

use App\Actions\CreateInvoice;
use App\Actions\DeleteInvoices;
use App\Actions\StoreInvoice;
use App\Actions\ViewHome;
use App\Actions\ViewInvoice;

function app_router(): ?object
{
    $uri = strpos($_SERVER['REQUEST_URI'], '?') ? substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?')) : $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];

    // GET routes
    if ($method === 'GET') {
        return match ($uri) {
            '/' => new ViewHome,
            '/create' => new CreateInvoice,
            '/delete' => new DeleteInvoices,
            '/view' => new ViewInvoice,
            default => null,
        };
    }

    // POST routes
    if ($method === 'POST') {
        return match ($uri) {
            '/create' => new StoreInvoice,
            default => null,
        };
    }
}
