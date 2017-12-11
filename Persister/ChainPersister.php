<?php

namespace Brouzie\Components\Indexer\Persister;

use Brouzie\Components\Indexer\Persister;

class ChainPersister implements Persister
{
    private $persisters;

    /**
     * @param iterable|Persister[] $persisters
     */
    public function __construct(iterable $persisters)
    {
        $this->persisters = $persisters;
    }

    public function persist(array $entries): void
    {
        foreach ($this->persisters as $persister) {
            $persister->persist($entries);
        }
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete(array $ids): void
    {
        foreach ($this->persisters as $persister) {
            $persister->persist($ids);
        }
    }

    public function clear(): void
    {
        foreach ($this->persisters as $persister) {
            $persister->clear();
        }
    }
}
