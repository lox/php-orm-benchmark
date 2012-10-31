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
		$book = (object) $table->query()->where('id=?', $id)->execute()->row();
		$title = $book->title;
	}
}

