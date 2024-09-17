<?php

require_once '../vendor/autoload.php';
require_once '../etc/config.php';

use App\Controller\DefaultController;
use Doctrine\DBAL\Connection;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\DriverManager;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


$route1 = new Route('/', ['_controller' => DefaultController::class, '_method' => 'index']);
$route2 = new Route('/about', ['_controller' => DefaultController::class, '_method' => 'about']);


$routes = new RouteCollection();
$routes->add('default', $route2);
$routes->add('about', $route2);

$context = new RequestContext();

$matcher = new UrlMatcher($routes, $context);
$parameters = $matcher->match(strtok($_SERVER["REQUEST_URI"], '?'));

$className = $parameters["_controller"];
$method = $parameters["_method"];

$request = Request::createFromGlobals();

$container = new DI\Container([
    Connection::class => function () {
        $connectionParams = [
            'dbname' => DB_NAME,
            'user' => DB_USER,
            'password' => DB_PASS,
            'host' => DB_HOST,
            'driver' => 'pdo_mysql',
        ];
        return DriverManager::getConnection($connectionParams);
    },
    Environment::class => function () {
        $loader = new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . '/../templates');
        return new Environment($loader, [
            //'cache' => $_SERVER['DOCUMENT_ROOT'] . '/../cache',
        ]);
    }
]);

$controller = $container->get(DefaultController::class);
$response = $controller->$method($request);
$response->send();
