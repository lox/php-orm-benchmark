<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');

function __autoload($class)
{
	require(__DIR__.'/1.1.0/lib/'.str_replace('\\','/', $class).'.php');
}

use \Pheasant;
use \Pheasant\Types;

class Author extends \Pheasant\DomainObject
{
	public function properties()
	{
		return array(
			'id' => new Types\Integer(11, 'primary'),
			'first_name' => new Types\String(255, 'required'),
			'last_name' => new Types\String(255, 'required'),
			'email' => new Types\String(255, 'required'),
		);
	}

	public function relationships()
	{
		return array(
			'Books' => Book::hasOne('author_id')
		);
	}
}

class Book extends \Pheasant\DomainObject
{
	public function properties()
	{
		return array(
			'id' => new Types\Integer(11, 'primary'),
			'title' => new Types\String(255, 'required'),
			'isbn' => new Types\String(24),
			'price' => new Types\Decimal(),
			'author_id' => new Types\Integer(11),
		);
	}

	public function relationships()
	{
		return array(
			'Author' => Author::hasOne('id')
		);
	}
}

class Pheasant_1_1_0_Benchmark extends BaseBenchmark
{
	public function setUp()
	{
		Pheasant::setup(sprintf(
			"mysqli://%s:%s@%s/%s",
			$this->user, $this->password, $this->host, $this->database
		));
	}

	public function benchInsert($author, $book)
	{
		$a = new Author(array(
			'id' => $author->id,
			'first_name' => $author->first_name,
			'last_name' => $author->last_name,
			'email' => $author->email
		));
		$a->save();

		$b = new Book(array(
			'id' => $book->id,
			'title' => $book->title,
			'isbn' => $book->isbn,
			'price' => $book->price,
			'author_id' => $a->id
		));
		$b->save();
	}

	public function benchPkSearch($id)
	{
		$book = Book::oneById($id);
		$title = $book->title;
	}

	public function benchEnumerate()
	{
		foreach(Book::find()->limit(10) as $idx=>$book)
		{
			$title = $book->title;
		}
	}

	public function benchSearch()
	{
		for($i=1; $i<=10; $i++)
			$count = Author::find("id > ? OR (first_name = 'John{$i}' OR last_name = 'Doe{$i}')", $i)->count();
	}


	public function benchNPlus1()
	{
		foreach(Book::find()->limit(10) as $idx=>$book)
		{
			$author = $book->Author;
		}
	}
}
