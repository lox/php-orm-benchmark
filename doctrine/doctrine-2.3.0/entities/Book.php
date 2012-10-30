<?php

namespace Proxies;

/** @Entity */
class Book
{
    /** @Id @Column(type="integer") */
    public $id;
    /** @Column */
    public $title;
    /** @Column(length=24) */
    public $isbn;
    /** @Column(type="decimal") */
    public $price;
    /** @ManyToOne(targetEntity="Author") cascade={"persist"} */
    public $author;
}
