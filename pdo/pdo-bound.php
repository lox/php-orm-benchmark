<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');

class Pdo_Bound_Benchmark extends BaseBenchmark
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
		$stmt1 = $this->pdo->prepare('INSERT INTO author VALUES (?,?,?,?)');
		$stmt1->bindValue(1, $author->id, PDO::PARAM_INT);
		$stmt1->bindValue(2, $author->first_name, PDO::PARAM_STR);
		$stmt1->bindValue(3, $author->last_name, PDO::PARAM_STR);
		$stmt1->bindValue(4, $author->email, PDO::PARAM_STR);
		$stmt1->execute();

		$stmt2 = $this->pdo->prepare('INSERT INTO book VALUES (?,?,?,?,?)');
		$stmt2->bindValue(1, $book->id, PDO::PARAM_INT);
		$stmt2->bindValue(2, $book->title, PDO::PARAM_STR);
		$stmt2->bindValue(3, $book->isbn, PDO::PARAM_STR);
		$stmt2->bindValue(4, $book->price, PDO::PARAM_STR);
		$stmt2->bindValue(5, $author->id, PDO::PARAM_INT);
		$stmt2->execute();
	}

	public function benchPkSearch($id)
	{
		$stmt = $this->pdo->prepare('SELECT * FROM book WHERE id=? LIMIT 1');
		$stmt->bindValue(1, $id, PDO::PARAM_INT);
		$stmt->execute();
		$book = (object)$stmt->fetch();
		$title = $book->title;
	}

	public function benchEnumerate()
	{
		$stmt = $this->pdo->prepare("SELECT * FROM book LIMIT 10");
		$stmt->execute();

		while($row = $stmt->fetch())
		{
			$book = (object) $row;
			$title = $book->title;
		}
	}

	public function benchSearch()
	{
		$sql = "SELECT count(a.id) AS num FROM author a WHERE a.id > ? OR (a.first_name = ? OR a.last_name = ?)";
		$stmt = $this->pdo->prepare($sql);

		for($i=1; $i<=10; $i++)
		{
			$stmt->bindValue(1, $i, PDO::PARAM_INT);
			$stmt->bindValue(2, "John{$i}", PDO::PARAM_STR);
			$stmt->bindValue(3, "Doe{$i}", PDO::PARAM_STR);
			$stmt->execute();

			$result = $stmt->fetch();
			$count = $result['num'];
		}
	}

	public function benchNPlus1()
	{
		$stmt = $this->pdo->prepare("SELECT * FROM author WHERE id=? LIMIT 1");
		$result = $this->pdo->query("SELECT * FROM book LIMIT 10");

		while($row = $result->fetch())
		{
			$stmt->bindValue(1, $row['author_id'], PDO::PARAM_INT);
			$stmt->execute();
			$author = $stmt->fetch();
		}
	}
}
