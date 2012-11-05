<?php

require_once(__DIR__.'/../lib/BaseBenchmark.php');
require_once __DIR__."/doctrine-2.3.0/vendor/autoload.php";

class Doctrine_2_3_0_Benchmark extends BaseBenchmark
{
	public $authors, $books;

	public function setUp()
	{
		$entityDir = __DIR__.'/doctrine-2.3.0/entities';
		$proxyDir = __DIR__.'/doctrine-2.3.0/proxies';

		$cache = new \Doctrine\Common\Cache\ArrayCache;
		$config = new Doctrine\ORM\Configuration;
		$config->setMetadataCacheImpl($cache);
		$driverImpl = $config->newDefaultAnnotationDriver($entityDir);
		$config->setMetadataDriverImpl($driverImpl);
		$config->setQueryCacheImpl($cache);
		$config->setProxyDir($proxyDir);
		$config->setProxyNamespace('Bookstore');
		$config->setAutoGenerateProxyClasses(false);

		$dbParams = array(
			'driver'   => 'pdo_mysql',
			'user'     => $this->user,
			'password' => $this->password,
			'dbname'   => $this->database,
		);

		$this->em = $em = \Doctrine\ORM\EntityManager::create($dbParams, $config);
		$this->em->clear();

		// generate proxies
		$proxyFactory = $em->getProxyFactory();
		$metadatas = $em->getMetadataFactory()->getAllMetadata();
		$proxyFactory->generateProxyClasses($metadatas, $proxyDir);

		require_once $proxyDir . '/__CG__BookstoreAuthor.php';
		require_once $proxyDir . '/__CG__BookstoreBook.php';
	}

	public function benchInsert($author, $book)
	{
		$this->em->beginTransaction();

		$a = new \Bookstore\Author();
		$a->id = $author->id;
		$a->first_name = $author->first_name;
		$a->last_name = $author->last_name;
		$a->email = $author->email;

		$b = new \Bookstore\Book();
		$b->id = $book->id;
		$b->title = $book->title;
		$b->author = $a;
		$b->isbn = $book->isbn;
		$b->price = $book->price;

		$this->em->persist($a);
		$this->em->persist($b);

		$this->em->flush();
		$this->em->commit();
		$this->em->clear();
	}

	public function benchPkSearch($id)
	{
		$author = $this->em->find('\Bookstore\Author', $id);
		$this->em->clear();
	}

	public function benchEnumerate()
	{
		$books = $this->em->createQuery('SELECT b FROM \Bookstore\Book b')->setMaxResults(10)->getResult();
		foreach ($books as $book) {
			$title = $book->title;
		}
		$this->em->clear();
	}

	public function benchSearch()
	{
		for($i=1; $i<=10; $i++)
		{
			$dql = 'SELECT count(a.id) AS num FROM \Bookstore\Author a WHERE a.id > ?1 OR (a.first_name = ?2 OR a.last_name = ?3)';
			$count = $this->em
				->createQuery($dql)
				->setParameter(1, $i)
				->setParameter(2, 'John'.$i)
				->setParameter(3, 'Doe'.$i)
				->setMaxResults(1)
				->getSingleScalarResult()
				;
		}
		$this->em->clear();
	}

	public function benchNPlus1()
	{
		$books = $this->em->createQuery('SELECT b FROM \Bookstore\Book b')->setMaxResults(10)->getResult();
		foreach ($books as $book) {
			$author = $book->author;
			$firstname = $author->first_name;
		}
		$this->em->clear();
	}
}
