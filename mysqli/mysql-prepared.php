<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');

class Mysqli_Prepared_Benchmark extends BaseBenchmark
{
	public function setUp()
	{
		$this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);
	}

	public function benchInsert($id, $author, $book)
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
}
