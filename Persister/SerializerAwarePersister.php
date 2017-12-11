<?php

namespace Brouzie\Components\Indexer\Persister;

use Brouzie\Components\Indexer\Persister;
use Brouzie\Components\Indexer\Serializer;

class SerializerAwarePersister implements Persister
{
    private $persister;

    private $serializer;

    public function __construct(Persister $persister, Serializer $serializer)
    {
        $this->persister = $persister;
        $this->serializer = $serializer;
    }

    public function persist(array $entries): void
    {
        $entries = array_map([$this->serializer, 'serialize'], $entries);
        $this->persister->persist($entries);
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete(array $ids): void
    {
        $this->persister->delete($ids);
    }

    public function clear(): void
    {
        $this->persister->clear();
    }
}
