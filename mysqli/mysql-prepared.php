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
		$stmt = $this->mysqli->prepare("SELECT * FROM book WHERE id=? LIMIT 1");
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$stmt->bind_result($id, $title, $isbn, $price, $author_id);
		$stmt->fetch();
	}

	public function benchEnumerate()
	{
		$stmt = $this->mysqli->prepare("SELECT * FROM book LIMIT 10");
		$stmt->execute();
		$result = $stmt->get_result();

		while($row = $result->fetch_assoc())
		{
			$book = (object) $row;
			$title = $book->title;
		}
	}

	public function benchSearch()
	{
		$sql = "SELECT count(a.id) AS num FROM author a WHERE a.id > ? OR (a.first_name = ? OR a.last_name = ?)";
		$stmt = $this->mysqli->prepare($sql);

		for($i=1; $i<=10; $i++)
		{
			$first_name = "John{$i}";
			$last_name = "Doe{$i}";

			$stmt->bind_param('iss', $i, $first_name, $last_name);
			$stmt->execute();
			$result = $stmt->get_result();
			$count = $result->fetch_row();
		}
	}

	public function benchNPlus1()
	{
		$stmt = $this->mysqli->prepare("SELECT * FROM author WHERE id=? LIMIT 1");
		$result = $this->mysqli->query("SELECT * FROM book LIMIT 10");

		while($row = $result->fetch_assoc())
		{
			$stmt->bind_param('i', $row['author_id']);
			$stmt->execute();

			$result = $stmt->get_result();
			$author = $result->fetch_assoc();
		}
	}
}
