<?php

namespace App\Services;

use App\Models\Book;
use App\Repositories\BookRepository;

class BookService
{
    private $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    public function listBooks()
    {
        $rawData = $this->bookRepository->listBooks();
        $books = [];

        while ($book = $rawData->fetch(\PDO::FETCH_ASSOC)) {
            $books[] = new Book(
                $book['id'],
                $book['name'],
                $book['author'],
                $book['genre'],
                $book['publication_date']
            );
        }

        return $books;
    }

    public function addBook($bookData)
    {
        $book = new Book(
            null,
            $bookData['name'],
            $bookData['Author'],
            $bookData['Genre'],
            $bookData['publicationDate']
        );

       $id = $this->bookRepository->addBook($book);
       $book->setId($id);

       return $book;
    }

    public function editBook($bookData, $bookId)
    {
        $book = new Book(
            $bookId,
            $bookData['name'],
            $bookData['Author'],
            $bookData['Genre'],
            $bookData['publicationDate']
        );

        $this->bookRepository->editBook($book);

        return $book;
    }

    public function deleteBook($bookId)
    {
        $this->bookRepository->deleteBook($bookId);
    }
}
