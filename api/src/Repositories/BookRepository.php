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
            ->from('Books')->execute();

        return $rows;
    }

    public function addBook(Book $book)
    {
        $unique = $this->checkUniqueness($book);
        if ($unique->rowCount() > 0)
            throw new \Exception('A book with the given data already exists');


        $this->dbConnection->createQueryBuilder()
            ->insert('Books')->values(
                array(
                    'name' => '?',
                    'author' => '?',
                    'genre' => '?',
                    'publication_date' => '?'
                ))
            ->setParameter(0, $book->getName())
            ->setParameter(1, $book->getAuthor())
            ->setParameter(2, $book->getGenre())
            ->setParameter(3, $book->getPublicationDate())
            ->execute();

        return $this->dbConnection->lastInsertId();
    }

    public function checkUniqueness(Book $book)
    {
        $unique = $this->dbConnection->createQueryBuilder()
            ->select('*')
            ->from('Books')
            ->where('name = ?')
            ->andWhere('author = ?')
            ->andWhere('genre = ?')
            ->andWhere('publication_date = ?')
            ->setParameter(0, $book->getName())
            ->setParameter(1, $book->getAuthor())
            ->setParameter(2, $book->getGenre())
            ->setParameter(3, $book->getPublicationDate())
            ->execute();
        return $unique;
    }

    public function editBook(Book $book)
    {
        $unique = $this->checkUniqueness($book);
        if ($unique->rowCount() > 1)
            throw new \Exception('A book with the given data already exists');
        if ($unique->rowCount() == 1 and $unique->fetchColumn(0) != $book->getId())
            throw new \Exception('A book with the given data already exists');

        $this->dbConnection->executeQuery(
            'UPDATE Books
                    SET `name` = ?,
                    author = ?,
                    genre = ?,
                    publication_date = ?
                    WHERE `id` = ?',
            [
                $book->getName(),
                $book->getAuthor(),
                $book->getGenre(),
                $book->getPublicationDate(),
                $book->getId()
            ]
        );
    }

    public function deleteBook($id)
    {
        $this->dbConnection->createQueryBuilder()
            ->delete('Books')
            ->where('id = ?')
            ->setParameter(0, $id)
            ->execute();
    }
}
