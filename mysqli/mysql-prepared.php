<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');

class Mysqli_Prepared_Benchmark extends BaseBenchmark
{
	public function setUp()
	{
		$this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);
	}

	public function benchInsert($author, $book)
	{
		if(!($stmt = $this->mysqli->prepare("INSERT INTO author VALUES (?, ?, ?, ?)")))
			throw new Exception($this->mysqli->error);

		$stmt->bind_param('isss', $author->id, $author->first_name, $author->last_name, $author->email);
		$stmt->execute();

		if(!($stmt = $this->mysqli->prepare("INSERT INTO book VALUES (?, ?, ?, ?, ?)")))
			throw new Exception($this->mysqli->error);

		$stmt->bind_param('issdd', $book->id, $book->title, $book->isbn, $book->price, $author->id);
		$stmt->execute();
	}

	public function benchPkSearch($id)
	{
		$stmt = $this->mysqli->prepare("SELECT * FROM book WHERE id={$id} LIMIT 1");
		$stmt->execute();
		$stmt->bind_result($id, $title, $isbn, $price, $author_id);
		$stmt->fetch();
	}
}
