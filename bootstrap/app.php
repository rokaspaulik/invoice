<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/router.php';
require __DIR__ . '/storage.php';
require __DIR__ . '/http.php';
require __DIR__ . '/twig.php';

$route = app_router();
is_object($route) ? $route() : http_status_not_found();
