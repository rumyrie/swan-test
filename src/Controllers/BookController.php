<?php

namespace App\Controllers;

use App\Services\BookService;
use Slim\Http\Request;
use Slim\Http\Response;

class BookController
{
    private $bookService;

    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    public function inputValidation($data, $schema)
    {
        $validator = new Validator();
        try {
            $validator->validate($data,
                (object)['$ref' => 'file://' . realpath("src/Schemas/" . $schema)],
                Constraint::CHECK_MODE_EXCEPTIONS);

            return $validator->isValid();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function listBooks(Request $request, Response $response, $args)
    {
        try {
            $books = $this->bookService->listBooks();
            return $response->withJson($books);

        } catch (\Exception $e) {
            return $response->withJson($e->getMessage());
        }
    }

    public function addBook(Request $request, Response $response, $args)
    {
        try {
            $book = $request->getParsedBody();
            $book = $this->bookService->addBook($book);

            return $response->withJson($book);
        } catch (\Exception $e) {
            return $response->withJson($e->getMessage());
        }
    }

    public function editBook(Request $request, Response $response, $args)
    {
        try {
            $bookId = $args['id'];
            $book = $request->getParsedBody();
            $this->bookService->addBook($book, $bookId);

            return $response->withJson($book);

        } catch (\Exception $e) {
            return $response->withJson($e->getMessage());
        }
    }

    public function deleteBook(Request $request, Response $response, $args)
    {
        try {
            $bookId = $args['id'];
            $this->bookService->deleteBook($bookId);

            return $response->withJson('success');

        } catch (\Exception $e) {
            return $response->withJson($e->getMessage());
        }
    }
}
