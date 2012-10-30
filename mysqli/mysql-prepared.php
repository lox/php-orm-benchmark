<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');

class Mysqli_Prepared_Benchmark extends BaseBenchmark
{
	public function setUp()
	{
		$this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);
	}

	public function benchAuthorInsert($id, $first_name, $last_name, $email)
	{
		if(!($stmt = $this->mysqli->prepare("INSERT INTO author VALUES (?, ?, ?, ?)")))
			throw new Exception($this->mysqli->error);

		$stmt->bind_param('isss', $id, $first_name, $last_name, $email);
		$stmt->execute();
	}

	public function benchBookInsert($id, $title, $author_id, $isbn, $price)
	{
		if(!($stmt = $this->mysqli->prepare("INSERT INTO book VALUES (?, ?, ?, ?, ?)")))
			throw new Exception($this->mysqli->error);

		$stmt->bind_param('issdd', $id, $title, $isbn, $price, $author_id);
		$stmt->execute();
	}
}
