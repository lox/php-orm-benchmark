<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');
require_once(__DIR__.'/1.0.0b3/autoload.php');

use \Pheasant\Database;
use \Pheasant\Database\Mysqli;

class Pheasant_1_0_0b3_Mysqli_Benchmark extends BaseBenchmark
{
	public function setUp()
	{
		$this->conn = new Mysqli\Connection(new Database\Dsn(sprintf(
			"mysqli://%s:%s@%s/%s",
			$this->user, $this->password, $this->host, $this->database
		)));
	}

	public function benchInsert($author, $book)
	{
		$table = $this->conn->table('author');
		$table->insert((array) $author);

		$book->author_id = $author->id;
		$table = $this->conn->table('book');
		$table->insert((array) $book);
	}

	public function benchPkSearch($id)
	{
		$table = $this->conn->table('book');
		$book = (object) $table->query()->where('id=?', $id)->limit(1)->execute()->row();
		$title = $book->title;
	}

	public function benchEnumerate()
	{
		$table = $this->conn->table('book');
		foreach($table->query()->limit(10)->execute() as $row)
		{
			$book = (object) $row;
			$title = $book->title;
		}
	}

	public function benchSearch()
	{
		for($i=1; $i<=10; $i++)
		{
			$sql = 'SELECT count(a.id) AS num FROM author a WHERE a.id > ? OR (a.first_name = ? OR a.last_name = ?)';
			$count = $this->conn->execute($sql, array($i, "John{$i}", "Doe$i"))->scalar();
		}
	}

	public function benchNPlus1()
	{
		$book = $this->conn->table('book');
		$author = $this->conn->table('author');

		foreach($book->query()->limit(10)->execute() as $row)
		{
			$a = $author->query()->where('id=?', $row['author_id'])->execute()->row();
		}
	}
}

