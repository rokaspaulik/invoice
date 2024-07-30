<?php

function store(string $filename, string $data)
{
    $path = __DIR__ . '/../storage/' . $filename . '.json';

    file_put_contents($path, $data);
}
