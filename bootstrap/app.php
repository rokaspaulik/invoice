<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/storage.php';
require __DIR__ . '/http.php';
require __DIR__ . '/twig.php';

use App\Actions\CreateInvoice;
use App\Actions\DeleteInvoices;
use App\Actions\StoreInvoice;
use App\Actions\ViewHome;
use App\Actions\ViewInvoice;

$uri = strpos($_SERVER['REQUEST_URI'], '?')
    ? substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'))
    : $_SERVER['REQUEST_URI'];

$method = $_SERVER['REQUEST_METHOD'];

// GET routes
if ($method === 'GET') {
    if ($uri === '/') {
        $invoke = new ViewHome;
        $invoke();
    }

    if ($uri === '/create') {
        $invoke = new CreateInvoice;
        $invoke();
    }

    if ($uri === '/delete') {
        $invoke = new DeleteInvoices;
        $invoke();
    }

    if ($uri === '/view') {
        $invoke = new ViewInvoice;
        $invoke();
    }
}

// POST routes
if ($method === 'POST') {
    if ($uri === '/create') {
        $invoke = new StoreInvoice;
        $invoke();
    }
}
