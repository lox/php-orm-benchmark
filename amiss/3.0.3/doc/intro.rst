Introduction
============

.. only:: latex

    .. include:: preamble.rst.inc


About the pattern
-----------------

The core of Amiss is the Data Mapper.

From Martin Fowler's `Patterns of Enterprise Application Architecture
<http://martinfowler.com/eaaCatalog/dataMapper.html>`_:

    A [Data Mapper is a] layer of Mappers (473) that moves data between objects  and a database
    while keeping them independent of each other and the mapper itself.

Amiss makes some small concessions to pragmatism that may offend domain model purists when you use
any of the :doc:`mapping methods <mapper/mapping>` that are in the core distribution, but overall it
does a passable job of keeping its grubby mitts off your model considering the small codebase.


About the examples
------------------

Most of the examples contained herein will make use of the schema and model that accompany the
documentation in the ``doc/demo`` subdirectory of the source distribution. It contains a very simple
set of related objects representing a set of events for a music festival.

There is also a set of examples in the - you guessed it - ``example`` folder in the source
distribution that will allow you to click through some scripts that are built on this schema.


Model Classes
-------------

The model classes referred to in the documentation are as follows:

.. include:: _build/example.rst
