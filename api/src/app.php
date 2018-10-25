<?php

use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;

use App\Controllers\BookController;
use App\Services\BookService;
use App\Repositories\BookRepository;
use Slim\Views\TwigExtension;

$container = $app->getContainer();

$container['db'] = function () {
    return DriverManager::getConnection([
        'driver' => 'pdo_mysql',
        'host' => 'localhost',
        'dbname' => 'bookStorage',
        'user' => 'root',
        'password' => 'root',
        'charset' => 'utf8',
    ]);
};

$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(__DIR__ . '/../../templates/', []);
    $basePath = rtrim(str_ireplace('index.php', '', $container->get('request')->getUri()->getBasePath()), '/');
    $view->addExtension(new TwigExtension($container->get('router'), $basePath));

    return $view;
};

$container['book.controller'] = function ($c) {
    /** @var ContainerInterface $c */

    return new BookController($c->get('book.service'), $c->get('view'));
};

$container['book.service'] = function ($c) {
    /** @var ContainerInterface $c */
    return new BookService($c->get('book.repository'));
};

$container['book.repository'] = function ($c) {
    /** @var ContainerInterface $c */
    return new BookRepository($c->get('db'));
};

require __DIR__ . '/router.php';
