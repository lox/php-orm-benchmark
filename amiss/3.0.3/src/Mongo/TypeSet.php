<?php
namespace Amiss\Mongo;

class TypeSet extends \ArrayObject
{
    function __construct()
    {
        $this[] = array(new Type\Id, array('id'));
        $this[] = array(new Type\Date, 'date');
        $this[] = array(new \Amiss\Type\Embed($mapper), array('embed'));
    }
}
