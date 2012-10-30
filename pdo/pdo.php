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

	public function benchAuthorInsert($id, $first_name, $last_name, $email)
	{
		$stmt = $this->pdo->prepare('INSERT INTO author VALUES (?,?,?,?)');
		$stmt->bindValue(1, $id, PDO::PARAM_INT);
		$stmt->bindValue(2, $first_name, PDO::PARAM_STR);
		$stmt->bindValue(3, $last_name, PDO::PARAM_STR);
		$stmt->bindValue(4, $email, PDO::PARAM_STR);
		$stmt->execute();
	}

	public function benchBookInsert($id, $title, $author_id, $isbn, $price)
	{
		$stmt = $this->pdo->prepare('INSERT INTO book VALUES (?,?,?,?,?)');
		$stmt->bindValue(1, $id, PDO::PARAM_INT);
		$stmt->bindValue(2, $title, PDO::PARAM_STR);
		$stmt->bindValue(3, $isbn, PDO::PARAM_STR);
		$stmt->bindValue(4, $price, PDO::PARAM_STR);
		$stmt->bindValue(5, $author_id, PDO::PARAM_INT);
		$stmt->execute();
	}
}
