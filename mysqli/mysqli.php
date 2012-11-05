<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');

class Mysqli_Benchmark extends BaseBenchmark
{
	public function setUp()
	{
		$this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);
	}

	public function benchInsert($author, $book)
	{
		$sql = sprintf("INSERT INTO author VALUES (%d, '%s', '%s', '%s')",
		 	$author->id, $author->first_name, $author->last_name, $author->email);

		if(!$this->mysqli->query($sql))
			throw new Exception($this->mysqli->error);

		$sql = sprintf("INSERT INTO book VALUES (%d, '%s', '%s', %f, %d)",
		 	$book->id, $book->title, $book->isbn, $book->price, $author->id);

		if(!$this->mysqli->query($sql))
			throw new Exception($this->mysqli->error);
	}

	public function benchPkSearch($id)
	{
		$book = (object) $this->mysqli->query("SELECT * FROM book WHERE id={$id} LIMIT 1")->fetch_assoc();
		$title = $book->title;
	}

	public function benchEnumerate()
	{
		$result = $this->mysqli->query("SELECT * FROM book LIMIT 10");

		while($row = $result->fetch_assoc())
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
			$result = $this->mysqli->query($sql);
			$count = $result->fetch_row();
		}
	}

	public function benchNPlus1()
	{
		$result = $this->mysqli->query("SELECT * FROM book LIMIT 10");

		while($row = $result->fetch_assoc())
		{
			$authorid = $row['author_id'];
			$sql = "SELECT * FROM author WHERE id={$authorid} LIMIT 1";
			$author_result = $this->mysqli->query($sql);
			$author = $author_result->fetch_assoc();
		}
	}
}
