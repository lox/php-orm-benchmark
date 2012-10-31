<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');

class Pdo_Benchmark extends BaseBenchmark
{
	public function setUp()
	{
		$this->pdo = new PDO(
			"mysql:host={$this->host};dbname={$this->database}",
			$this->user,
			$this->password
		);
	}

	public function benchInsert($author, $book)
	{
		$sql = sprintf("INSERT INTO author VALUES (%d, '%s', '%s', '%s')",
		 	$author->id, $author->first_name, $author->last_name, $author->email);

		$this->pdo->exec($sql);

		$sql = sprintf("INSERT INTO book VALUES (%d, '%s', '%s', %f, %d)",
		 	$book->id, $book->title, $book->isbn, $book->price, $author->id);

		$this->pdo->exec($sql);
	}

	public function benchPkSearch($id)
	{
		$book = (object) $this->pdo->query("SELECT * FROM book WHERE id={$id} LIMIT 1")->fetch();
		$title = $book->title;
	}
}


