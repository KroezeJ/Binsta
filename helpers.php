<?php

function error($errorNumber, $errorMessage)
{
    http_response_code($errorNumber);
    $loader = new \Twig\Loader\FilesystemLoader('../views');
    $twig = new \Twig\Environment($loader);
    $template = $twig->load('error.twig');
    echo $template->render(['errorMessage' => $errorMessage]);
    exit;
}
