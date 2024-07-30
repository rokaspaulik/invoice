<?php

function http_redirect(string $uri)
{
    header("Location: $uri");
}

function http_status_not_found()
{
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found', true, 404);
    view('404');
    die;
}
