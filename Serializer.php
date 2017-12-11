<?php

namespace Brouzie\Components\Indexer;

interface Serializer
{
    /**
     * @param mixed $object
     */
    public function serialize($object): Entry;
}
