<?php

function redirect(string $uri)
{
    header("Location: $uri");
}
