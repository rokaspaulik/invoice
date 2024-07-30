<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../views');
$twig = new Environment($loader);

function view(string $name, array $context = [])
{
    global $twig;

    $template = $twig->load($name . '.html.twig');
    
    echo $template->render($context);
}
