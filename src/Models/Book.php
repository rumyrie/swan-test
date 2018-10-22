<?php
/**
 * Created by PhpStorm.
 * User: Meer
 * Date: 22.10.2018
 * Time: 18:10
 */

namespace App\Models;


class Book implements \JsonSerializable
{
    private $id;
    private $name;
    private $author;
    private $genre;
    private $publication_date;

    /**
     * Book constructor.
     * @param $id int
     * @param $name string
     * @param $author string
     * @param $genre string
     * @param $publication_date \DateTime
     */
    public function __construct($id, $name, $author, $genre, $publication_date)
    {
        $this->id = $id;
        $this->name = $name;
        $this->author = $author;
        $this->genre = $genre;
        $this->publication_date = $publication_date;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'author' => $this->author,
            'genre' => $this->genre,
            'publication_date' => $this->publication_date
        ];
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @return \DateTime
     */
    public function getPublicationDate()
    {
        return $this->publication_date;
    }

}
