<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');

class Mysqli_Benchmark extends BaseBenchmark
{
	public function setUp()
	{
		$this->mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);
	}

	public function benchAuthorInsert($id, $first_name, $last_name, $email)
	{
		$sql = sprintf("INSERT INTO author VALUES (%d, '%s', '%s', '%s')",
		 	$id, $first_name, $last_name, $email);

		if(!$this->mysqli->query($sql))
			throw new Exception($this->mysqli->error);
	}

	public function benchBookInsert($id, $title, $author_id, $isbn, $price)
	{
		$sql = sprintf("INSERT INTO book VALUES (%d, '%s', '%s', %f, %d)",
		 	$id, $title, $isbn, $price, $author_id);

		if(!$this->mysqli->query($sql))
			throw new Exception($this->mysqli->error);
	}
}
