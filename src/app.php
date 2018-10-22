<?php

use Doctrine\DBAL\DriverManager;
use Psr\Container\ContainerInterface;

use App\Controllers\BookController;
use App\Services\BookService;
use App\Repositories\BookRepository;


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

$container['book.controller'] = function ($c) {
    /** @var ContainerInterface $c */
    return new BookController($c->get('book.service'));
};

$container['book.service'] = function ($c) {
    /** @var ContainerInterface $c */
    return new BookService($c->get('book.'));
};

$container['book.repository'] = function($c) {
    /** @var ContainerInterface $c */
    return new BookRepository($c->get('db'));
};

require __DIR__ . '/router.php';
