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

	public function benchInsert($id, $author, $book)
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
}
