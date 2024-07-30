<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/twig.php';

use App\Actions\CreateInvoice;
use App\Actions\StoreInvoice;
use App\Actions\ViewHome;

$uri = $_SERVER['REQUEST_URI'];
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
}

// POST routes
if ($method === 'POST') {
    if ($uri === '/create') {
        $invoke = new StoreInvoice;
        $invoke();
    }
}
