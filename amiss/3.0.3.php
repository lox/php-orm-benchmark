<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');

// Include and register autoloader (optional)
require_once(__DIR__.'/3.0.3/src/Loader.php');
Amiss\Loader::register();

class Author
{
	/**
	 * @primary
	 */
	public $id;

	/**
	 * @field
	 */
	public $first_name;

	/**
	 * @field
	 */
	public $last_name;

	/**
	 * @field
	 */
	public $email;

	/** @has many of=Book; on=author_id */
	public $books;
}

class Book
{
	/**
	 * @primary
	 */
	public $id;

	/**
	 * @field
	 */
	public $title;

	/**
	 * @field
	 */
	public $isbn;

	/**
	 * @field
	 */
	public $price;

	/**
	 * @field
	 */
	public $author_id;

	/** @has one of=Book; on=author_id */
	public $author;
}

class Amiss_3_0_3_Benchmark extends BaseBenchmark
{
	public function setUp()
	{
		$mapper = new Amiss\Mapper\Note;

		$connector = new Amiss\Sql\Connector('mysql:host='.$this->host.
			';dbname='.$this->database , $this->user, $this->password);

		$this->manager = new Amiss\Sql\Manager($connector, $mapper);
	}

	public function benchInsert($author, $book)
	{
		$a = new Author();
		$a->id = $author->id;
		$a->first_name = $author->first_name;
		$a->last_name = $author->last_name;
		$a->email = $author->email;

		$this->manager->insert($a);

		$b = new Book();
		$b->id = $book->id;
		$b->title = $book->title;
		$b->isbn = $book->isbn;
		$b->price = $book->price;
		$b->author_id = $book->id;

		$this->manager->insert($b);
	}

	public function benchPkSearch($id)
	{
		$book = $this->manager->getById('Book', $id);
		$title = $book->title;
	}

	public function benchEnumerate()
	{
		foreach($this->manager->getList('Book', array('limit'=>10)) as $book)
		{
			$title = $book->title;
		}
	}

	public function benchSearch()
	{
		for($i=1; $i<=10; $i++)
		{
			$count = $this->manager->count(
				'Author', 'id > :id OR ( first_name=:first_name OR last_name=:last_name )',
				array(':id'=>$i, ':first_name'=>"John{$i}", ':last_name'=>"Doe{$i}")
			);
		}
	}

	public function benchNPlus1()
	{
		$books = $this->manager->getList('Book', array('limit'=>10));
		$this->manager->assignRelated($books, 'author');
		foreach ($books as $book) {
			$author = $book->author;
		}
	}
}
