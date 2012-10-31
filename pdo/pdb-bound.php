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


}
