PHP ORM Benchmarks
==================

As part of the development of [Pheasant](http://github.com/lox/pheasant), I wanted to compare
performance of common ORM operations to other PHP ORM's.

Existing benchmarks ran against Sqlite, which is unsupported by Pheasant. This project containts
roughly the same tests, but executed against a MySQL instance.

What are the tests?
-------------------

* Author & Book Insert
A row is inserted into the `author` table with a value for `first_name` and `last_name`.  A row is
inserted into the `book` table with a value for `title`, `isbn`, `price` and `author_id`.

* Author PK Search
A single author is fetched by `author.id`

* Enumerate Books
All books are fetched and sequentially enumerated as objects.

* Author OR Search
All authors where `author.id > X OR (author.first_name = Y OR author.last_name = Y)` are enumerated
as objects.

* Enumerate Books Authors
All books are enumerated, and the associated `author` is accessed.

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



