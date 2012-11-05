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

	public function benchEnumerate()
	{
		foreach($this->pdo->query("SELECT * FROM book LIMIT 10") as $row)
		{
			$book = (object) $row;
			$title = $book->title;
		}
	}

	public function benchSearch()
	{
		for($i=1; $i<=10; $i++)
		{
			$sql = "SELECT count(a.id) AS num FROM author a WHERE a.id > {$i} OR (a.first_name = 'John{$i}' OR a.last_name = 'Doe{$i}')";
			$result = $this->pdo->query($sql)->fetch();
			$count = $result['num'];
		}
	}

	public function benchNPlus1()
	{
		$result = $this->pdo->query("SELECT * FROM book LIMIT 10");

		while($row = $result->fetch())
		{
			$author = $this->pdo->query("SELECT * FROM author WHERE id={$row['author_id']} LIMIT 1")->fetch();
		}
	}
}


