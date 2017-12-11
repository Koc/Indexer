<?php

namespace Brouzie\Components\Indexer\Serializer;

use Brouzie\Components\Indexer\Entry;
use Brouzie\Components\Indexer\Entry\GenericEntry;
use Brouzie\Components\Indexer\Serializer;

class SimpleArraySerializer implements Serializer
{
    private $idField;

    public function __construct(string $idField)
    {
        $this->idField = $idField;
    }

    public function serialize($object): Entry
    {
        return new GenericEntry($object[$this->idField], $object);
    }
}
