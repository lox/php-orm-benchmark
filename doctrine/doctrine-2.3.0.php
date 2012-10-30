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
		$config->setProxyNamespace('Proxies');
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

		require_once $proxyDir . '/__CG__ProxiesAuthor.php';
		require_once $proxyDir . '/__CG__ProxiesBook.php';
	}

	public function benchAuthorInsert($id, $first_name, $last_name, $email)
	{
		$this->em->beginTransaction();

		$author = new \Proxies\Author();
		$author->id = $id;
		$author->first_name = $first_name;
		$author->last_name = $last_name;
		$author->email = $email;

		$this->em->persist($author);
		$this->authors[$id]= $author;

		$this->em->flush();
		$this->em->commit();
	}

	public function benchBookInsert($id, $title, $author_id, $isbn, $price)
	{
		$this->em->beginTransaction();

		$book = new \Proxies\Book();
		$book->id = $id;
		$book->title = $title;
		$book->author = $this->authors[$id];
		$book->isbn = $isbn;
		$book->price = $price;

		$this->em->persist($book);

		$this->em->flush();
		$this->em->commit();
	}
}
