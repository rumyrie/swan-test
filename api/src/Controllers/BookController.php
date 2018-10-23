<?php

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig as View;

use App\Services\BookService;

class BookController
{
    private $bookService;

    public function __construct(BookService $bookService, View $view)
    {
        $this->bookService = $bookService;
        $this->view = $view;
    }

    public function loadPage(Request $request, Response $response)
    {
        return $this->view->render($response, 'books.html');
    }

    public function listBooks(Request $request, Response $response)
    {
        try {
            $books = $this->bookService->listBooks();

            return $response->withJson($books, 200);

        } catch (\Exception $e) {
            return $response->withJson($e->getMessage());
        }
    }

    public function addBook(Request $request, Response $response)
    {
        try {
            $book = $request->getParsedBody();
            $book = $this->bookService->addBook($book);

            return $response->withJson($book, 200);

        } catch (\Exception $e) {
            return $response->withJson($e->getMessage());
        }
    }

    public function editBook(Request $request, Response $response, $args)
    {
        try {
            $bookId = $args['id'];
            $bookData = $request->getParsedBody();
            $book = $this->bookService->editBook($bookData, $bookId);
            return $response->withJson($book, 200);

        } catch (\Exception $e) {
            return $response->withJson($e->getMessage());
        }
    }

    public function deleteBook(Request $request, Response $response, $args)
    {
        try {
            $bookId = $args['id'];
            $this->bookService->deleteBook($bookId);

            return $response->withStatus(200);

        } catch (\Exception $e) {
            return $response->withJson($e->getMessage());
        }
    }
}
