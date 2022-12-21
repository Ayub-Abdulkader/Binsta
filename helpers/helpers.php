<?php

function dislayTemplate($template, $var = array()) 
{
    $loader = new \Twig\Loader\FilesystemLoader('./../views');
    $twig = new \Twig\Environment($loader);
    $twig->addGlobal('session', $_SESSION);
    echo $twig->render($template, $var);
}

function error($errorNumber, $errorMessage = array())
{
    // return error-twig
    $loader = new \Twig\Loader\FilesystemLoader('./../views');
    $twig = new \Twig\Environment($loader);
    echo $twig->render('error.twig', $errorMessage, $errorNumber);
    exit;
}