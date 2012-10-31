<?php

abstract class BaseBenchmark
{
	const ITERATIONS=1000;

	public
		$host,
		$user,
		$password,
		$database
		;

	public function __construct($host='localhost', $user='root', $password='', $database='phpormbenchmark')
	{
		$this->host = $host;
		$this->user = $user;
		$this->password = $password;
		$this->database = $database;
	}

	public function createTables()
	{
		$mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);

		if ($mysqli->connect_errno)
			throw new Exception( "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);

		$mysqli->query("DROP TABLE author");
		$mysqli->query("DROP TABLE book");

		$mysqli->query("
			CREATE TABLE book (
			id INTEGER  NOT NULL PRIMARY KEY,
			title VARCHAR(255)  NOT NULL,
			isbn VARCHAR(24)  NOT NULL,
			price FLOAT,
			author_id INTEGER
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");

		$mysqli->query("
			CREATE TABLE author (
				id INTEGER  NOT NULL PRIMARY KEY,
				first_name VARCHAR(128)  NOT NULL,
				last_name VARCHAR(128)  NOT NULL,
				email VARCHAR(128)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8
		");
	}

	public function checksum()
	{
		$mysqli = new mysqli($this->host, $this->user, $this->password, $this->database);
		$book = $mysqli->query("SELECT MD5(GROUP_CONCAT(id,title,isbn,price,author_id)) FROM book")->fetch_array();
		$author = $mysqli->query("SELECT MD5(GROUP_CONCAT(id,first_name,last_name,email)) FROM author")->fetch_array();

		return array(
			'book' => $book[0],
			'author' => $author[0],
		);
	}

	public function validateChecksum($book, $author)
	{
		$checksums = $this->checksum();
		return $book == $checksums['book'] && $checksums['author'] == $author;
	}

	public function benchmark()
	{
		$this->createTables();
		$this->setUp();

		printf("%-30s", get_class($this));

		// Benchmark #1 Insert
		// --------------------------

		$start = microtime(true);

		for($i=1; $i<=self::ITERATIONS; $i++)
		{
			$author = (object) array(
				'id' => $i,
				'first_name' => 'John'.$i,
				'last_name' => 'Doe'.$i,
				'email' => "johndoe{$i}@gmail.com",
			);

			$book = (object) array(
				'id' => $i,
				'title' => 'Book '.$i,
				'isbn' => '1234',
				'price' => 19.95
			);

			$this->benchInsert($author, $book);
		}

		if($this->validateChecksum('fe27e58fa3232b4157381bb4818c6184', '2931e7d371cd97310c4951dec972b95a'))
			printf("%-15s", number_format((microtime(true)-$start) * 1000, 4).'ms' );
		else
			printf("BAD MD5");

		// Benchmark #3 Author PK search
		// -----------------------------

		$start = microtime(true);

		for($i=1; $i<=self::ITERATIONS; $i++)
		{
			$this->benchPkSearch($i);
		}

		if($this->validateChecksum('fe27e58fa3232b4157381bb4818c6184', '2931e7d371cd97310c4951dec972b95a'))
			printf("%-15s", number_format((microtime(true)-$start) * 1000, 4).'ms' );
		else
			printf("BAD MD5");

		printf("\n");
	}

	public function setUp()
	{
	}

	public abstract function benchInsert($author, $book);
	public abstract function benchPkSearch($id);

}

// ------------------------------
// automatically execute tests

register_shutdown_function(function() {
	foreach(get_declared_classes() as $class)
	{
		if(is_subclass_of($class, 'BaseBenchmark'))
		{
			$b = new $class();
			$b->benchmark();
		}
	}
});

