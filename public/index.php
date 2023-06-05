<?php

require_once '../vendor/autoload.php';

use RedBeanPHP\R as R;

if (!isset($_SESSION)) {
    session_start();
}

$loader = new \Twig\Loader\FilesystemLoader('../views');
$twig = new \Twig\Environment($loader);

$host = 'localhost';
$dbname = 'binsta';
$username = 'bit_academy';
$password = 'bit_academy';

if (!R::testConnection()) {
    R::setup("mysql:host=$host;dbname=$dbname", $username, $password);
}

$usernames = R::findAll('users');

$twig->addGlobal('session', $_SESSION);
$twig->addGlobal('users', $usernames);

$path = $_SERVER['REQUEST_URI'];

$path = str_replace($_SERVER['SCRIPT_NAME'], '', $path);
$path = trim($path, '/');

$segments = explode('/', $path);

if (empty($segments[0])) {
    $segments[0] = 'feed';
}

$controller = ucfirst($segments[0]) . 'Controller';
$method = isset($segments[1]) ? $segments[1] : 'index';

if (str_contains($method, '?')) {
    $other = explode('?', $method);
    $method = $other[0];
    if (str_contains($other[1], '=')) {
        $value1 = explode('=', $other[1]);
        if ($value1[0] == 'name') {
            $value = $value1[1];
        }
    }
}


if (!class_exists($controller)) {
    error(404, 'Controller ' . $controller . ' not found');
}

$controllerInstance = new $controller();

if (!method_exists($controllerInstance, $method)) {
    error(404, 'Method not found');
}

if (isset($value)) {
    $controllerInstance->$method($twig, $value);
} else {
    $controllerInstance->$method($twig);
}

?>

<link rel="stylesheet"
      href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/styles/atom-one-light.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.7.0/highlight.min.js"></script>
<script>hljs.highlightAll();</script>