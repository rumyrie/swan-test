<?php

namespace App\Repositories;

use Doctrine\DBAL\Connection;
use App\Models\Book;

class BookRepository
{
    /**
     * @var Connection
     */
    protected $dbConnection;

    public function __construct(Connection $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function listBooks()
    {
        $rows = $this->dbConnection
            ->createQueryBuilder()
            ->select('id', 'name', 'author, genre', 'publication_date')
            ->from('Book')->execute();

        return $rows;
    }

    public function addBook(Book $book)
    {
        $this->checkUniqueness(book);

        $this->dbConnection->createQueryBuilder()
            ->insert('Book')->values(
                array(
                    'id' => '?',
                    'name' => '?',
                    'author' => '?',
                    'genre' => '?',
                    'publication_date' => '?'
                ))
            ->setParameter(0, $book->getId())
            ->setParameter(1, $book->getName())
            ->setParameter(2, $book->getAuthor())
            ->setParameter(3, $book->getGenre())
            ->setParameter(4, $book->getPublicationDate())
            ->execute();

        return $this->dbConnection->lastInsertId();
    }

    public function checkUniqueness(Book $book)
    {
        $unique = $this->dbConnection->createQueryBuilder()
            ->select('*')
            ->from('Book')
            ->where('name = ?')
            ->andWhere('author = ?')
            ->andWhere('genre = ?')
            ->andWhere('publication_date = ?')
            ->setParameter(1, $book->getName())
            ->setParameter(2, $book->getAuthor())
            ->setParameter(3, $book->getGenre())
            ->setParameter(4, $book->getPublicationDate())
            ->execute()->rowCount();

        if ($unique != 0)
            throw new \Exception('A book with the given data already exists.');
    }

    public function editBook(Book $book)
    {
        $this->checkUniqueness($book);

        $this->dbConnection->createQueryBuilder()
            ->update('Book')
            ->where('id = ?')
            ->set('id', '?')
            ->set('name', '?')
            ->set('author', '?')
            ->set('genre', '?')
            ->set('publication_date', '?')
            ->setParameter(0, $book->getId())
            ->setParameter(1, $book->getName())
            ->setParameter(2, $book->getAuthor())
            ->setParameter(3, $book->getGenre())
            ->setParameter(4, $book->getPublicationDate())
            ->execute();
    }

    public function deleteBook($id)
    {
        $this->dbConnection->createQueryBuilder()
            ->delete('Book')
            ->where('id = ?')
            ->setParameter(0, $id)
            ->execute();
    }
}
