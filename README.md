PHP ORM Benchmarks
==================

As part of the development of [Pheasant](http://github.com/lox/pheasant), I wanted to compare
performance of common ORM operations to other PHP ORM's.

Existing benchmarks ran against Sqlite, which is unsupported by Pheasant. This project contains
roughly the same tests, but executed against a MySQL instance.

For reference, see https://github.com/eventhorizonpl/forked-php-orm-benchmark

Test #1 `INSERT`: Author & Book Insert
--------------------------------------

For 1 to 1000 rows:

  * Insert 1000 rows into `book` table with `{id: $i, first_name: 'John$i', 'last_name': 'Doe$i}'}`
  * Insert 1000 rows into `author` table with {id: {$i}, title: 'Book $i', isbn: '1234', price: $i}, author_id: $i}`

Test #2: `PK_SEARCH` Author PK Search
-------------------------------------

For 1 to 1000, fetch author by id as an object.

Test #3: `ENUMERATE` Enumerate first 10 Books
---------------------------------------------

The first 10 books are fetched and sequentially enumerated as objects.

Test #4: `SEARCH` Author OR Search
------------------------------------------

For 1 to 10, fetch the count of authors where `author.id > $i OR (author.first_name = 'John$i' OR author.last_name = 'Doe$i')`

Test #5: `N_PLUS_1` Enumerate Books Authors
-------------------------------------------

The first 10 books are enumerated, and the associated `author` is accessed.

Schema
------

```sql
CREATE TABLE book (
	id INTEGER  NOT NULL PRIMARY KEY,
	title VARCHAR(255)  NOT NULL,
	isbn VARCHAR(24)  NOT NULL,
	price FLOAT,
	author_id INTEGER
) ENGINE=InnoDB DEFAULT CHARSET=utf8;;

CREATE TABLE author (
	id INTEGER  NOT NULL PRIMARY KEY,
	first_name VARCHAR(128)  NOT NULL,
	last_name VARCHAR(128)  NOT NULL,
	email VARCHAR(128)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

```sql
# To verify after insert:
SELECT MD5(GROUP_CONCAT(a.id, a.first_name, a.last_name, a.email, b.id, b.title, b.isbn, b.price)) FROM book b, author a WHERE a.id=b.author_id;
# => 3aa133a39da9d73211b0dfaa729cf412
```



