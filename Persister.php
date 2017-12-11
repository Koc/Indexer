<?php

namespace Brouzie\Components\Indexer;

interface Persister
{
    /**
     * @param object[]|Entry[] $entries
     */
    public function persist(array $entries): void;

    public function update();

    public function delete(array $ids): void;

    public function clear(): void;
}
