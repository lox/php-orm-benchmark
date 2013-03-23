Annotation Mapper
=================

`Amiss\Sql\Manager` uses ``Amiss\Mapper\Note`` with certain :doc:`types` preconfigured by default.
This should be used as a default starting point. You can access the mapper for further configuration
after you create the manager like so:

.. code-block:: php

    <?php
    $manager = new \Amiss\Sql\Manager($db, $cache);
    $mapper = $manager->mapper;


This is the equivalent of the following:

.. code-block:: php

    <?php
    $mapper = new \Amiss\Mapper\Note($cache);
    $mapper->addTypeSet(new \Amiss\Sql\TypeSet);
    $manager = new \Amiss\Sql\Manager($db, $mapper);


See :doc:`common` and :doc:`types` for more information on how to tweak Amiss' default mapping
behaviour.


Overview
--------

Using the Annotation mapper, object/table mappings are defined in this way:

.. code-block:: php

    <?php
    /**
     * @table your_table
     * @fieldType VARCHAR(255)
     */
    class Foo
    {
        /** @primary */
        public $id;

        /** @field some_column */
        public $name;

        /** @field */
        public $barId;

        /** 
         * One-to-many relation:
         * @has many of=Bar 
         */
        public $bars;

        /**
         * One-to-one relation: 
         * @has one of=Baz; on=bazId
         */
        public $baz;

        // field is defined below using getter/setter
        private $fooDate;

        /**
         * @field
         * @type date
         */
        public function getFooDate()
        {
            return $this->fooDate;
        }

        public function setFooDate($value)
        {
            $this->fooDate = $value;
        }
    }

It is assumed by this mapper that an object and a table are corresponding entities. More complex
mapping should be handled using a :doc:`custom mapper <custom>`.


Annotations
-----------

Annotations are javadoc-style key/values and are formatted like so:

.. code-block:: php

    <?php
    /**
     * @key this is the value
     */


The ``Amiss\Note\Parser`` class is used to extract these annotations. Go ahead and use it in your
own application if you find it useful, but keep in mind the following:

 * Everything up to the first space is considered the key. Use whatever symbols 
   you like for the key as long as it isn't whitespace.

 * The value starts after the first space after the key and ends at the first newline. 
   Currently, RFC 2822 style folding is not supported (though it may be in future if it 
   is needed by Amiss). The value is *not trimmed for whitespace*.

 * Multiple annotations per line are *not supported*.


Class Mapping
-------------

The following class level annotations are available:

.. py:attribute:: @table value

    When declared, this forces the mapper to use this table name. It may include a schema name as
    well. If not provided, the table name will be determined by the mapper. See 
    :ref:`name-translation` for details on this process.


.. py:attribute:: @fieldType value

    This sets a default field type to use for for all of the properties that do not have a field
    type set against them explicitly. This will inherit from a parent class if one is set. See
    :doc:`types` for more details.


These values must be assigned in the class' docblock:

.. code-block:: php

    <?php
    /**
     * @table my_table
     * @fieldType string-a-doodle-doo
     */
    class Foo
    {}


Property mapping
----------------

Mapping a property to a column is done inside a property or getter method's docblock.

The following annotations are available to define this mapping:

.. py:attribute:: @field columnName

    This marks whether a property or a getter method represents a value that should be stored in a
    column.

    The ``columnName`` value is optional. If it isn't specified, the column name is determined by
    the base mapper. See :ref:`name-translation` for more details on this process.


.. py:attribute:: @type fieldType

    Optional type for the field. If this is not specified, the ``@fieldType`` class level attribute
    is used. See :doc:`types` for more details.


.. py:attribute:: @setter setterName

    If the ``@field`` attribute is set against a getter method as opposed to a property, this
    defines the method that is used to set the value when loading an object from the database. It is
    required if the ``@field`` attribute is defined against a property that has a getter/setter name
    pair that doesn't follow the traditional ``getFoo``/``setFoo`` pattern.

    See :ref:`annotations-getters-setters` for more details.


Relation mapping
----------------

Mapping an object relation is done inside a property or getter method's docblock.

The following annotations are available to define this mapping:

.. py:attribute:: @has relationType relationParams

    Defines a relation against a property or getter method.

    ``relationType`` must be a short string registered with ``Amiss\Sql\Manager->relators``. The
    ``one``, ``many`` and ``assoc`` relators are available by default.

    ``relationParams`` allows you to pass an array of key/value pairs to instruct the relator
    referred to by ``relationType`` how to handle retrieving the related objects.

    ``relationParams`` is basically a query string with a few enhancements. Under the hood, Amiss
    just uses PHP's stupidly named `parse_str <http://php.net/parse_str>`_ function. You can use
    anything you would otherwise be able to use in a query string, like:

        * ``url%20encoding%21``
        * ``space+encoding``
        * ``array[parameters]=yep``
        * ``many=values&are=ok``
    
    As well as a few bits of syntactic sugar that gets cleaned up before parsing, like:
        
        * ``semicolon=instead;of=ampersand;for=readability``
        * ``whitespace = around ; separators = too``
    
    You're free to use whatever you feel will be most readable, but my personal preference is for
    this format, which is used throughout this guide::

        foo=bar; this=that; array[a]=yep
    
    This saves Amiss the trouble of requiring you to learn a complicated annotation syntax to
    represent complex data, with the added benefit of being mostly implemented in C.

    **One-to-one** (``@has one``) relationships require, at a minimum, the target object of the
    relation and the field(s) on which the relation is established. You should read the 
    :ref:`relator-one` documentation for a full description of the data this relator requires. A
    simple one-to-one is annotated like so:

    .. code-block:: php
        
        <?php
        class Artist
        {
            /** @primary */
            public $artistId;

            /** @field */
            public $artistTypeId;
            
            /** @has one of=ArtistType; on=artistTypeId
            public $artist;
        }
    

    A one-to-one relationship where the left and right side have different field names::

        @has one of=ArtistType; on[typeId]=artistTypeId


    A one-to-one relationship on a composite key::

        @has one of=ArtistType; on[]=typeIdPart1; on[]=typeIdPart2


    A one-to-one relationship on a composite key with different field names::

        @has one of=ArtistType; on[typeIdPart1]=idPart1; on[typeIdPart2]=idPart2
        
    
    A one-to-one relationship with a matching one-to-many on the related object, where the ``on``
    values are to be determined from the related object::
        
        @has one of=ArtistType; inverse=artist
    
    
    **One-to-many** (``@has many``) relationships support all the same options as one-to-one
    relationships. You should read the :ref:`relator-many` documentation for a full description of 
    the data this relator requires. The simplest one-to-many is annotated like so:

    .. code-block:: php

        <?php
        class ArtistType
        {
            /** @primary */
            public $artistTypeId;

            /** @has many of=Artist; on=artistTypeId */
            public $artists;
        }


    **Association** (``@has assoc``) relationships are annotated quite differently. You should read
    the :ref:`relator-assoc` documentation for a full description of the data this relator requires.
    A quick example:

    .. code-block:: php

        <?php
        class Event
        {
            /** @primary */
            public $eventId;

            /** @has many of=EventArtist; on=eventId */
            public $eventArtists;

            /** @has assoc of=Artist; via=EventArtist */
            public $artists;
        }
    



.. py:attribute:: @setter setterName

    If the ``@has`` attribute is set against a getter method as opposed to a property, this defines
    the method that is used to set the value when loading an object from the database. It is
    required if the ``@has`` attribute is defined against a property and the getter/setter method
    names deviate from the standard ``getFoo``/``setFoo`` pattern.

    See :ref:`annotations-getters-setters` for more details.


.. _annotations-getters-setters:

Getters and setters
-------------------

Properties should almost always be defined against your object as class-level fields in PHP. Don't
use getters and setters when you are doing no more than getting or setting a private field value -
it's a total waste of resources. See my `stackoverflow answer
<http://stackoverflow.com/a/813099/15004>`_ for a more thorough explanation of why you shouldn't,
and for a brief explanation of how to get all of the benefits anyway.

Having said that, getters and setters are essential when you need to do more than just set a private
value.

Getters and setters can be used for both fields and relations. When using the annotation mapper,
this should be done against the getter in exactly the same way as you would do it against a
property:

.. code-block:: php

    <?php
    class Foo
    {
        private $baz;
        private $qux;

        /** @field */
        public function getBaz()
        {
            return $this->baz;
        }

        /** @has one of=Qux; on=baz */
        public function getQux()
        {
            return $this->qux;
        }
    }

There is a problem with the above example: we have provided a way to get the values, but not to set
them. This will make it impossible to retrieve the object from the database. If you provide matching
``setBaz`` and ``setQux`` methods, Amiss will guess that these are paired with ``getBaz`` and
``getQux`` respectively and don't require any special annotations:

.. code-block:: php

    <?php
    class Foo
    {
        // snip

        public function setBaz($value)
        {
            $value->thingy = $this;
            $this->baz = $value;
        }

        public function setQux($value)
        {
            $value->thingy = $this;
            $this->qux = $value;
        }
    }


If your getter/setter pair doesn't follow the ``getFoo/setFoo`` standard, you can specify the setter
directly against both relations and fields using the ``@setter`` annotation. The following example
should give you some idea of my opinion on going outside the standard, but Amiss tries not to be too
opinionated so you can go ahead and make your names whatever you please:

.. code-block:: php

    <?php
    class Foo
    {
        private $baz;
        private $qux;

        /** 
         * @field
         * @setter assignAValueToBaz
         */
        public function getBaz()
        {
            return $this->baz;
        }

        public function assignAValueToBaz($value)
        {
            $value->thingy = $this;
            $this->baz = $value;
        }

        /** 
         * @has one of=Qux; on=baz
         * @setter makeQuxEqualTo
         */
        public function pleaseGrabThatQuxForMe() 
        
            return $this->qux;
        }

        public function makeQuxEqualTo($value)
        {
            $value->thingy = $this;
            $this->qux = $value;
        }
    }


Caching
-------

``Amiss\Mapper\Note`` provides a facility to cache reflected metadata. This is not strictly
necessary: the mapping process only does a little bit of reflection and is really very fast, but you
can get up to 30% more speed out of Amiss in circumstances where you're doing even just a few
metadata lookups per request (say, running one or two queries against one or two objects) by using a
cache.

The simplest way to enable caching is to create an instance of ``Amiss\Cache`` with a callable
getter and setter as the first two arguments, then pass it as the first constructor argument of
``Amiss\Maper\Note``. Many of the standard PHP caching libraries can be used in this way:

.. code-block:: php

    <?php
    $cache = new \Amiss\Cache('apc_fetch', 'apc_store');
    $cache = new \Amiss\Cache('xcache_get', 'xcache_set');
    $cache = new \Amiss\Cache('eaccelerator_get', 'eaccelerator_put');
    
    // when using the SQL manager's default note mapper:
    $manager = new \Amiss\Sql\Manager($db, $cache);
    
    // when creating the mapper by hand
    $mapper = new \Amiss\Mapper\Note($cache);


By default, no TTL or expiration information will be passed by the mapper. In the case of
``apc_store``, for example, this will mean that once cached, the metadata will never invalidate.
If you would like an expiration to be passed, you can either pass it as the fourth argument
to the cache's constructor (the third argument is explained later), or set it against the
``expiration`` property:

.. code-block:: php

    <?php
    // Using the constructor
    $cache = new \Amiss\Cache('apc_fetch', 'apc_store', null, 86400);

    // Or setting by hand
    $cache = new \Amiss\Cache('apc_fetch', 'apc_store');
    $cache->expiration = 86400;


You can set a prefix for the cache in case you want to ensure Amiss does not clobber items that
other areas of your application may be caching::

.. code-block:: php

    <?php
    $cache = new Amiss\Cache('xcache_get', 'xcache_set');
    $cache->prefix = 'dont-tread-on-me-';
    

You can also use closures:

.. code-block:: php

    <?php
    $cache = new \Amiss\Cache(
        function ($key) {
            // get the value from the cache
        },
        function ($key, $value, $expiration) {
            // set the value in the cache
        }
    );


If you would rather use your own caching class, you can pass it directly to ``Amiss\Mapper\Note``
if it has following method signatures:

.. code-block:: php

    <?php
    class MyCache
    { 
        public function get($key) {}
        public function set($key, $value, $expiration=null) {}
    }
    $cache = new MyCache;
    $mapper = new Amiss\Mapper\Note($cache);


The ``$expiration`` parameter to ``set()`` is optional. It will be passed, but you can ignore it
and PHP doesn't require that it be present in your method signature.

If your class does not support this interface, you can use ``Amiss\Cache`` to wrap your own class
by passing the names of the getter and setter methods and your own class:

.. code-block:: php

    <?php
    class MyCache
    { 
        public function fetch($key) {}
        public function put($key, $value) {}
    }
    $cache = new MyCache;
    $cacheAdapter = new Amiss\Cache('fetch', 'put', $cache);
    $mapper = new Amiss\Mapper\Note($cacheAdapter);


.. warning:: 

    Don't use a cache in your development environment otherwise you'll have to clear the cache
    every time you change your models!

    Set an environment variable (see `SetEnv
    <https://httpd.apache.org/docs/2.2/mod/mod_env.html#setenv>`_  for apache or ``export`` for
    bash), then do something like this:

    .. code-block:: php
        
        <?php
        // give it a better name than this!
        $env = getenv('your_app_environment');
        
        $cache = null;
        if ($env != 'dev')
            $cache = new \Amiss\Cache('apc_fetch', 'apc_store');
        
        $mapper = new \Amiss\Mapper\Note($cache);

