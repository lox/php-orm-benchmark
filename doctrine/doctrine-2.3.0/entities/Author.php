<?php

namespace Bookstore;

/** @Entity */
class Author
{
    /** @Id @Column(type="integer") */
    public $id;
    /** @Column(length=255) */
    public $first_name;
    /** @Column(length=255) */
    public $last_name;
    /** @Column(length=255, nullable=true) */
		public $email;
    /** @OneToMany(targetEntity="Book", mappedBy="author") */
    public $books;
}
