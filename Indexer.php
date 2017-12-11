<?php

namespace Brouzie\Components\Indexer;

interface Indexer
{
    /**
     * Performs full reindex.
     */
    public function reindex(): void;

    /**
     * Performs partial reindex of items by given ids.
     */
    public function reindexIds(array $ids): void;

    public function update(array $data, array $criteria): void;

    public function delete(array $ids): void;

    public function clear(): void;
}
