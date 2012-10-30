<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');
require_once(__DIR__.'/1.0.0b3/autoload.php');

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

class Pheasant_1_0_0b3_Benchmark extends BaseBenchmark
{
	public $authors;

	public function setUp()
	{
		Pheasant::setup(sprintf(
			"mysqli://%s:%s@%s/%s",
			$this->user, $this->password, $this->host, $this->database
		));
	}

	public function benchAuthorInsert($id, $first_name, $last_name, $email)
	{
		$author = new Author(array(
			'id' => $id,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'email' => $email
		));
		$author->save();
		$this->authors[$id] = $author;
	}

	public function benchBookInsert($id, $title, $author_id, $isbn, $price)
	{
		$book = new Book(array(
			'id' => $id,
			'title' => $title,
			'isbn' => $isbn,
			'price' => $price,
			'author_id' => $author_id
		));
		$book->save();
	}
}




